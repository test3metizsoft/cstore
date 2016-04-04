-- verifie les mouvements de stock en doublon
select
    sm_product_id,
    sm_description,
    count(*)
from 
    stock_movement
where
    sm_description like '%Shipment for order%'
group by
    sm_product_id,
    sm_description
having
    count(*) > 1

--- verifie les expeditions et commandes non cr��s en mouvement de stock mouvements de stock 

select s.entity_id as shipment_id,
       s.increment_id as shipment_incremend_id,
       s.order_id as order_id
from
    sales_flat_shipment s,
    sales_flat_shipment_item si
where
    s.entity_id = si.parent_id
    and si.entity_id NOT IN (select sm_ui from stock_movement where sm_ui IS NOT NULL and sm_type = 'order')
order by
    s.entity_id DESC;

    
-- when you deleted product or shipemnt, some shipment can be blocked because of unique constraint on sm_ui
-- ex : you find this in ERP logs : SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1089' for key 'sm_ui''
-- the goal of this is to identify the stock movement related to deleted product and deleted shipments
-- you must delete them to fix the issue

--first do a backup of the table stock movement like : "stock_movement_save"

-- 1) identify deleted product
select
    sm_id,
    sm_product_id,
    sm_description,
    sm_type,
    sm_ui
from
    stock_movement
where
    sm_product_id NOT IN (select entity_id from catalog_product_entity)
order by
    sm_id DESC;

---Fix :
DELETE FROM stock_movement where sm_id IN (
select
    sm_id
from
    stock_movement_save
where
    sm_product_id NOT IN (select entity_id from catalog_product_entity)
)

--first do a backup of the table stock movement like : "stock_movement_save"
--- 2 ) identify deleted shipments
select
    sm_id,
    sm_product_id,
    sm_description,
    sm_type,
    sm_ui
from
    stock_movement_save
where
    sm_ui IS NOT NULL
    AND sm_ui NOT IN (select entity_id from sales_flat_shipment_item)
order by
    sm_id DESC;

---Fix :
DELETE FROM stock_movement where sm_id IN (
select
    sm_id
from
    stock_movement_save
where
    sm_ui IS NOT NULL
    AND sm_ui NOT IN (select entity_id from sales_flat_shipment_item)
)