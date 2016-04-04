<html>
<body>
<h1>CStore Master API test</h1>

<strike><b>1. Order Details {user_checkout_cartorder.php}</b></strike><br>
<form name="login" method="POST" action="">
<br>
customer_id: <input type="text" name="customer_id">
order_id: <input type="text" name="order_id">
<input type="submit" name="submit" value="submit"><br>
</form>
<hr>
<b>2. Region info depending on CountrySelection {region_list.php}</b>
<form name="login" method="POST" action="region_list.php">
<br>
country_id: <input type="text" name="country_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>3. Country Selection for add address {country_list.php}</b>
<form name="login" accept-charset="utf-8" method="POST" action="country_list.php">
<br>
<input type="submit" name="submit" value="Submit"><br>

</form>
<hr>


<b>4. Add product Review {Add_product_review.php}</b>
<form name="login" method="POST" action="Add_product_review.php">
<br>
product_id: <input type="text" name="product_id">
title: <input type="text" name="title">
detail: <input type="text" name="detail">
nickname: <input type="text" name="nickname">
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>

</form>
<hr>

<b>5. Change User Password {user_change_pass.php}</b>
<form name="login" method="POST" action="user_change_pass.php">
<br>
customer_id: <input type="text" name="customer_id">
password: <input type="text" name="password">
new_password: <input type="text" name="new_password">
<input type="submit" name="submit" value="Submit"><br>

</form>
<hr>

<b>6. Keyword search {search.php}</b>
<form name="login" method="POST" action="search.php">
<br>
customer_id: <input type="text" name="customer_id">
name: <input type="text" name="name">
<input type="submit" name="submit" value="Search"><br>
</form>
<hr>

<b>7. Advanced  Search Webservice {adv_search_products.php}</b>
<form name="login" method="POST" action="adv_search_products.php">
<br>
user_id: <input type="text" name="user_id">
name: <input type="text" name="name">
description: <input type="text" name="description">
short_description: <input type="text" name="short_description">
sku: <input type="text" name="sku">
start_price: <input type="text" name="start_price">
end_price: <input type="text" name="end_price">
tax_class_id: <input type="text" name="tax_class_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>8. Delivery Areas web service {city_list.php}</b>
<form name="login" method="POST" action="city_list.php">
<br>
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>9. User Shopping cart {get_cartdata.php}</b>
<form name="login" method="POST" action="get_cartdata.php">
<br>
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>10. Add product to wishlist {Add_product_wishlist.php}</b>
<form name="login" method="POST" action="Add_product_wishlist.php">
<br>
customer_id: <input type="text" name="customer_id">
product_id: <input type="text" name="product_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>11. Add product to cart {add_product_cart.php}</b>
<form name="login" method="POST" action="add_product_cart.php">
<br>
customer_id: <input type="text" name="customer_id">
product_id: <input type="text" name="product_id">
qty: <input type="text" name="qty">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>12. Products search  depending on Category or subcategory id {category_product.php}</b>
<form name="login" method="POST" action="category_product.php">
<br>
customer_id: <input type="text" name="customer_id">
category_id: <input type="text" name="category_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>13. User Product subcategories {product_sub_category.php}</b>
<form name="login" method="POST" action="product_sub_category.php">
<br>
category_id: <input type="text" name="category_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>14. Product Categories {product_category.php}</b>
<form name="login" method="POST" action="product_category.php">
<br>
level: <input type="text" name="level">
parent_id: <input type="text" name="parent_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>15. Home Screen Banners {home_banner.php}</b>
<form name="login" method="POST" action="home_banner.php">
<br>
UserId: <input type="text" name="UserId">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>16. My Wishlist {sales_product_wishlist.php}</b>
<form name="logout" method="POST" action="sales_product_wishlist.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>17. User Product Reviews {sales_product_review.php}</b>
<form name="logout" method="POST" action="sales_product_review.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>


<b>18. My Orders {sales_orders.php}</b>
<form name="logout" method="POST" action="sales_orders.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>19. Add user address (shipping and Billing) {add_usr_address.php}</b>
<form name="logout" method="POST" action="add_usr_address.php">
<br>
customer_id: <input type="text" name="customer_id">
firstname: <input type="text" name="firstname">
lastname: <input type="text" name="lastname">
company: <input type="text" name="company">
street: <input type="text" name="street">
city: <input type="text" name="city">
region: <input type="text" name="region">
region_id: <input type="text" name="region_id">
statecounty: <input type="text" name="statecounty">
postcode: <input type="text" name="postcode">
telephone: <input type="text" name="telephone">
fax: <input type="text" name="fax">
is_default_billing: <input type="text" name="is_default_billing">
is_default_shipping: <input type="text" name="is_default_shipping">
outofcityarea: <input type="text" name="outofcityarea">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>20. User Address book {get_address.php}</b>
<form name="logout" method="POST" action="get_address.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>21. User Account Info {view_user_info.php}</b>
<form name="logout" method="POST" action="view_user_info.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>22. User forget Password {user_forgot_pass.php}</b>
<form name="logout" method="POST" action="user_forgot_pass.php">
<br>
email: <input type="text" name="email">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>23. User Registration {cus_create.php}</b>
<form name="logout" method="POST" action="cus_create.php">
<br>
firstname: <input type="text" name="firstname">
lastname: <input type="text" name="lastname">
email: <input type="text" name="email">
password: <input type="text" name="password">

<br>
company: <input type="text" name="company">
street: <input type="text" name="street">
city: <input type="text" name="city">
region: <input type="text" name="region">
region_id: <input type="text" name="region_id">
statecounty: <input type="text" name="statecounty">
postcode: <input type="text" name="postcode">
telephone: <input type="text" name="telephone">
fax: <input type="text" name="fax">
is_default_billing: <input type="text" name="is_default_billing">
is_default_shipping: <input type="text" name="is_default_shipping">
outofcityarea: <input type="text" name="outofcityarea">

<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<strike><b>24. User Registration - Edit {cus_edit.php}</b></strike>
<form name="logout" method="POST">
<br>
firstname: <input type="text" name="firstname">
lastname: <input type="text" name="lastname">
email: <input type="text" name="email">
password: <input type="text" name="password">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>25. User Login  {user_login.php}</b>
<form name="logout" method="POST" action="user_login.php">
<br>
email: <input type="text" name="email">
password: <input type="text" name="password">
<input type="submit" name="submit" value="Login"><br>
</form>
<hr>

<b>26. Product update cart {user_checkout_productupdate.php}</b>
<form name="logout" method="POST" action="user_checkout_productupdate.php">
<br>
customer_id: <input type="text" name="customer_id">
product_id: <input type="text" name="product_id">
qty: <input type="text" name="qty">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>27. Product remove cart {user_checkout_productdelete.php}</b>
<form name="logout" method="POST" action="user_checkout_productdelete.php">
<br>
customer_id: <input type="text" name="customer_id">
product_id: <input type="text" name="product_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>28. Cart - Add Shipping address {user_checkout_shippingadd.php}</b>
<form name="logout" method="POST" action="user_checkout_shippingadd.php">
<br>
customer_id: <input type="text" name="customer_id">
customer_address_id: <input type="text" name="customer_address_id">
firstname: <input type="text" name="firstname">
lastname: <input type="text" name="lastname">
company: <input type="text" name="company">
street: <input type="text" name="street">
city: <input type="text" name="city">
region_id: <input type="text" name="region_id">
statecounty: <input type="text" name="statecounty">
postcode: <input type="text" name="postcode">
telephone: <input type="text" name="telephone">
fax: <input type="text" name="fax">
outofcityarea: <input type="text" name="outofcityarea">
save_in_address_book: <input type="text" name="save_in_address_book">
same_as_billing: <input type="text" name="same_as_billing">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>29. Cart - shipping list  {user_checkout_cartshippinglist.php}</b>
<form name="logout" method="POST" action="user_checkout_cartshippinglist.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>30. Cart - paymnet list  {user_checkout_getpaymentlist.php}</b>
<form name="logout" method="POST" action="user_checkout_getpaymentlist.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>31. Cart - add coupan code {user_checkout_coupan.php}</b>
<form name="logout" method="POST" action="user_checkout_coupan.php">
<br>
customer_id: <input type="text" name="customer_id">statecounty: <input type="text" name="statecounty">
coupan_code: <input type="text" name="coupan_code">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>32. Cart - remove coupan code {user_checkout_coupanremove.php}</b>
<form name="logout" method="POST" action="user_checkout_coupanremove.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>33. Cart - get total cart price {user_checkout_carttotalprice.php}</b>
<form name="logout" method="POST" action="user_checkout_carttotalprice.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>34. Cart - get cart info {user_checkout_cartinfo.php}</b>
<form name="logout" method="POST" action="user_checkout_cartinfo.php">

customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>35. Get Product Reviews {sales_product_review.php}</b>
<form name="logout" method="POST" action="sales_product_review.php">
<br>
customer_id: <input type="text" name="customer_id">
product_id: <input type="text" name="product_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>36. Get Order Details {sales_order_details.php}</b>
<form name="logout" method="POST" action="sales_order_details.php">
<br>
increment_id: <input type="text" name="increment_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>37. Get Country Name {country_name.php}</b>
<form name="logout" method="POST" action="country_name.php">
<br>
country_id: <input type="text" name="country_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>38. Cart - Place Order {user_checkout_cartorder.php}</b>
<form name="logout" method="POST" action="user_checkout_cartorder.php">
<br>
customer_id: <input type="text" name="customer_id">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>39. Cart - Set Payment Method {user_checkout_setpayment.php}</b>
<form name="logout" method="POST" action="user_checkout_setpayment.php">
<br>
customer_id: <input type="text" name="customer_id">
payment_method: <input type="text" name="payment_method">
cc_type:<select name="cc_type">
	<option>Please select</option>
	<option value="VI">Visa</option>
	<option value="MC">MasterCard</option>
	<option value="DI">Discover</option>
</select>
cc_number: <input type="text" name="cc_number">
cc_exp_year: <input type="text" name="cc_exp_year">
cc_exp_month: <input type="text" name="cc_exp_month">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>40. Cart - Set Shipment Method {user_checkout_setshipment.php}</b>
<form name="logout" method="POST" action="user_checkout_setshipment.php">
<br>
customer_id: <input type="text" name="customer_id">
shipment_method: <input type="text" name="shipment_method">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>41. Cart - Add Billing address {user_checkout_billingadd.php}</b>
<form name="logout" method="POST" action="user_checkout_billingadd.php">
<br>
customer_id: <input type="text" name="customer_id">
customer_address_id: <input type="text" name="customer_address_id">
firstname: <input type="text" name="firstname">
lastname: <input type="text" name="lastname">
company: <input type="text" name="company">
salestaxid: <input type="text" name="salestaxid">
street: <input type="text" name="street">
city: <input type="text" name="city">
region_id: <input type="text" name="region_id">
statecounty: <input type="text" name="statecounty">
postcode: <input type="text" name="postcode">
telephone: <input type="text" name="telephone">
fax: <input type="text" name="fax">
save_in_address_book: <input type="text" name="save_in_address_book">
use_for_shipping: <input type="text" name="use_for_shipping">
outofcityarea: <input type="text" name="outofcityarea">
<input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>42. Cart - get cart info {user_cartinfo.php}</b>
<form name="logout" method="POST" action="user_cartinfo.php">
    <br>
    customer_id: <input type="text" name="customer_id">
    <input type="submit" name="submit" value="Submit"><br>
</form>
<hr>
<b>43. Get product detail by id {getproductdetail.php}</b>
<form name="logout" method="POST" action="getproductdetail.php">
    <br>
    customer_id: <input type="text" name="customer_id">
    Product id: <input type="text" name="id">
    <input type="submit" name="submit" value="Submit"><br>
</form>
<hr>
<b>44. Get State/Province {getstate.php}</b>
<form name="logout" method="POST" action="getstate.php">
    <br>
    <input type="submit" name="submit" value="Submit"><br>
</form>
<hr>
<b>45. GetCounty  {getcounty.php}</b>
<form name="logout" method="POST" action="getcounty.php">
    <br>
    state_id: <input type="text" name="state_id">
    <input type="submit" name="submit" value="Submit"><br>
</form>
<hr>
<b>46. GetCity  {getcity.php}</b>
<form name="logout" method="POST" action="getcity.php">
    <br>
    county_id: <input type="text" name="county_id">
    <input type="submit" name="submit" value="Submit"><br>
</form>
<hr>
<b>47. Multiple cart api  {multiplecartapi.php}</b>
<form name="logout" method="POST" action="multiplecartapi.php">
    <br>
    customer_id: <input type="text" name="customer_id">
    product_id_1: <input type="text" name="data[0][id]">
    qty_1: <input type="text" name="data[0][qty]"><br>
    product_id_2: <input type="text" name="data[1][id]">
    qty_2: <input type="text" name="data[1][qty]"><br>
    product_id_3: <input type="text" name="data[2][id]">
    qty_3: <input type="text" name="data[2][qty]"><br>
    <input type="submit" name="submit" value="Submit"><br>
</form>
<hr>

<b>48. product listing with tax {product_tax.php}</b>
<form name="login" method="POST" action="search.php">
<br>
name: <input type="text" name="name">
<input type="submit" name="submit" value="Search"><br>

</form>
<hr>

<b>49. Clear cart of the customer {clearcart.php}</b>
<form name="login" method="POST" action="clearcart.php">
<br>
Customer id: <input type="text" name="cid" placeholder="Customer Id">
<input type="submit" name="submit" value="Clear Cart"><br>

</form>
<hr>
<b>50. User Login and add product in cart{user_login_cart.php}</b>
<form name="logout" method="POST" action="user_login_cart.php">
<br>
email: <input type="text" name="email">
password: <input type="text" name="password">
<br>
product_id_1: <input type="text" name="data[0][id]">
    qty_1: <input type="text" name="data[0][qty]"><br>
product_id_2: <input type="text" name="data[1][id]">
    qty_2: <input type="text" name="data[1][qty]"><br>
product_id_3: <input type="text" name="data[2][id]">
    qty_3: <input type="text" name="data[2][qty]"><br>
    
<input type="submit" name="submit" value="Login"><br>
</form>
<hr><b style="color:red; ">Below Api for Mobile Admin</b><hr>
<b>51. Sku Search {adminappskusearch.php}</b>
<form name="login" method="POST" action="adminappskusearch.php">
<br>
Sku: <input type="text" name="sku" placeholder="Sku">
UPC: <input type="text" name="upc" placeholder="Upc">
RetailUPC: <input type="text" name="retailupc" placeholder="RetailUPC">
<input type="submit" name="submit" value="Skusearch"><br>

</form>
<hr>
<b>52. Product Insert {adminproductinsert.php}</b>
<form name="productinsert" method="POST" action="adminproductinsert.php">
<br>
Sku: <input type="text" name="product[sku]" placeholder="Product Sku"><br />
Category 1: <input type="text" value="21" name="product[cat][]" placeholder="Category 1"><br />
Category 2: <input type="text" value="12" name="product[cat][]" placeholder="Category 2"><br />
Name: <input type="text" name="product[name]" placeholder="Product Name"><br />
Description: <textarea name="product[desc]" col="3" >Description</textarea><br />
Sort Description: <input type="text" name="product[sdesc]" placeholder="Sort Description"><br />
Weight: <input type="text" name="product[weight]" placeholder="Weight"><br />
Status: 
<select name="product[status]">
    <option value="1">Enable</option>
    <option value="2">Disabled</option>
</select><br />
Visibility: 
<select name="product[visi]">
    <option value="1">Not Visible Individually</option>
    <option value="2">Catalog</option>
    <option value="3">Search</option>
    <option selected="selected" value="4">Catalog, Search</option>
</select><br />
Price: <input type="text" name="product[price]" placeholder="Price"><br />
Unit in box: <input type="text" name="product[unitinbox]" placeholder="Unit in box"><br />
Tax Unit: <input type="text" name="product[taxunit]" placeholder="Tax Unit"><br />
Images: <input type="text" name="product[images][]" placeholder="Product Images 1" value="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAp1JREFUeNqEU21IU1EYfu7unW5Ty6aBszYs6MeUjGVYokHYyH5E1B9rZWFEFPQnAwmy6Hc/oqhfJsRKSSZGH1JIIX3MNCsqLTD9o1Oj6ebnnDfvvefezrnbdCHhCw/n433P8z7nPe/hBEEAtX0U7hc164uwuvVSXKwZLoOmaRDim+7m9vZa0WiEKSUFFpNpCWlmMyypqTDRuYn6t3k8vmQ2gRDCxs0t9fW45F52aBTROJLtZl7nEZad2m+KtoQCQ0FBARyOCGRZ/q92I1WgqqXlfdd95VsrK8/pChIEqqpCkiQsiCII0aBQZZoWl8lzFDwsFjMl0DBLY8Lj41hBwK4jSQrWOIphL6xYyhwJDWGo6wFSaH1Y3PTCAsITE1oyAa8flhWkbSiCLX8vun11eiGIpiJ/z2nYdx5HqLdVV7elrOzsuqysL3rmBIGiKPizKCHHWY4PLVeQbnXAdegqdhy+hu8dDTBnbqQJZJ1A7u+vz7RaiymWCZgCRSF6Edk8b9cx+B/W6WuVxPaZnyiqXoPpyUmVYvkKTIFClHigEieKjYuSvETUllaF4GAUM1NT6ooaJDKx+aDfC9fByxj90REb+9ppmIoAscH/6leg8MS9DJXPAM9xHCM443K57C6biMjcHDaVVCHw9RmCA2/RGC5C00AqXk/m4p20HZK4CM/J3Zk9n0ecMBhDQnJHcrTisyMfdQXOilrdMfxcwoHq/fg5R59TiQV3hYGKo6X2J/c7LyQIjOx9GXhOw/zoJ8wEevRGyp53o/lGMNYsBgPtEwLecwov7/jGDKa1twT6o3KpL4MdZgGsWZLtfPr7f1q58k1JNHy7YYaM+J+K3Y2PmAIbRavX66229hrGVvvL5uzsHDEUvUu+NT1my78CDAAMK1a8/QaZCgAAAABJRU5ErkJggg=="><br />
Images: <input type="text" name="product[images][]" placeholder="Product Images 2" value="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAp1JREFUeNqEU21IU1EYfu7unW5Ty6aBszYs6MeUjGVYokHYyH5E1B9rZWFEFPQnAwmy6Hc/oqhfJsRKSSZGH1JIIX3MNCsqLTD9o1Oj6ebnnDfvvefezrnbdCHhCw/n433P8z7nPe/hBEEAtX0U7hc164uwuvVSXKwZLoOmaRDim+7m9vZa0WiEKSUFFpNpCWlmMyypqTDRuYn6t3k8vmQ2gRDCxs0t9fW45F52aBTROJLtZl7nEZad2m+KtoQCQ0FBARyOCGRZ/q92I1WgqqXlfdd95VsrK8/pChIEqqpCkiQsiCII0aBQZZoWl8lzFDwsFjMl0DBLY8Lj41hBwK4jSQrWOIphL6xYyhwJDWGo6wFSaH1Y3PTCAsITE1oyAa8flhWkbSiCLX8vun11eiGIpiJ/z2nYdx5HqLdVV7elrOzsuqysL3rmBIGiKPizKCHHWY4PLVeQbnXAdegqdhy+hu8dDTBnbqQJZJ1A7u+vz7RaiymWCZgCRSF6Edk8b9cx+B/W6WuVxPaZnyiqXoPpyUmVYvkKTIFClHigEieKjYuSvETUllaF4GAUM1NT6ooaJDKx+aDfC9fByxj90REb+9ppmIoAscH/6leg8MS9DJXPAM9xHCM443K57C6biMjcHDaVVCHw9RmCA2/RGC5C00AqXk/m4p20HZK4CM/J3Zk9n0ecMBhDQnJHcrTisyMfdQXOilrdMfxcwoHq/fg5R59TiQV3hYGKo6X2J/c7LyQIjOx9GXhOw/zoJ8wEevRGyp53o/lGMNYsBgPtEwLecwov7/jGDKa1twT6o3KpL4MdZgGsWZLtfPr7f1q58k1JNHy7YYaM+J+K3Y2PmAIbRavX66229hrGVvvL5uzsHDEUvUu+NT1my78CDAAMK1a8/QaZCgAAAABJRU5ErkJggg=="><br />
<input type="submit" name="submit" value="Product Insert"><br>

</form>
<hr>
<b>52. Product Update {adminproductupdate.php}</b>
<form name="productinsert" method="POST" action="adminproductupdate.php">
<br>
Sku: <input type="text" name="product[sku]" placeholder="Product Sku"><br />
Category 1: <input type="text" value="21" name="product[cat][]" placeholder="Category 1"><br />
Category 2: <input type="text" value="12" name="product[cat][]" placeholder="Category 2"><br />
Name: <input type="text" name="product[name]" placeholder="Product Name"><br />
Description: <textarea name="product[desc]" col="3" >Description</textarea><br />
Sort Description: <input type="text" name="product[sdesc]" placeholder="Sort Description"><br />
Weight: <input type="text" name="product[weight]" placeholder="Weight"><br />
Status: 
<select name="product[status]">
    <option value="1">Enable</option>
    <option value="2">Disabled</option>
</select><br />
Visibility: 
<select name="product[visi]">
    <option value="1">Not Visible Individually</option>
    <option value="2">Catalog</option>
    <option value="3">Search</option>
    <option selected="selected" value="4">Catalog, Search</option>
</select><br />
Price: <input type="text" name="product[price]" placeholder="Price"><br />
Unit in box: <input type="text" name="product[unitinbox]" placeholder="Unit in box"><br />
Tax Unit: <input type="text" name="product[taxunit]" placeholder="Tax Unit"><br />
Images: <input type="text" name="product[images][]" placeholder="Product Images 1" value="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAp1JREFUeNqEU21IU1EYfu7unW5Ty6aBszYs6MeUjGVYokHYyH5E1B9rZWFEFPQnAwmy6Hc/oqhfJsRKSSZGH1JIIX3MNCsqLTD9o1Oj6ebnnDfvvefezrnbdCHhCw/n433P8z7nPe/hBEEAtX0U7hc164uwuvVSXKwZLoOmaRDim+7m9vZa0WiEKSUFFpNpCWlmMyypqTDRuYn6t3k8vmQ2gRDCxs0t9fW45F52aBTROJLtZl7nEZad2m+KtoQCQ0FBARyOCGRZ/q92I1WgqqXlfdd95VsrK8/pChIEqqpCkiQsiCII0aBQZZoWl8lzFDwsFjMl0DBLY8Lj41hBwK4jSQrWOIphL6xYyhwJDWGo6wFSaH1Y3PTCAsITE1oyAa8flhWkbSiCLX8vun11eiGIpiJ/z2nYdx5HqLdVV7elrOzsuqysL3rmBIGiKPizKCHHWY4PLVeQbnXAdegqdhy+hu8dDTBnbqQJZJ1A7u+vz7RaiymWCZgCRSF6Edk8b9cx+B/W6WuVxPaZnyiqXoPpyUmVYvkKTIFClHigEieKjYuSvETUllaF4GAUM1NT6ooaJDKx+aDfC9fByxj90REb+9ppmIoAscH/6leg8MS9DJXPAM9xHCM443K57C6biMjcHDaVVCHw9RmCA2/RGC5C00AqXk/m4p20HZK4CM/J3Zk9n0ecMBhDQnJHcrTisyMfdQXOilrdMfxcwoHq/fg5R59TiQV3hYGKo6X2J/c7LyQIjOx9GXhOw/zoJ8wEevRGyp53o/lGMNYsBgPtEwLecwov7/jGDKa1twT6o3KpL4MdZgGsWZLtfPr7f1q58k1JNHy7YYaM+J+K3Y2PmAIbRavX66229hrGVvvL5uzsHDEUvUu+NT1my78CDAAMK1a8/QaZCgAAAABJRU5ErkJggg=="><br />
Images: <input type="text" name="product[images][]" placeholder="Product Images 2" value="iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAp1JREFUeNqEU21IU1EYfu7unW5Ty6aBszYs6MeUjGVYokHYyH5E1B9rZWFEFPQnAwmy6Hc/oqhfJsRKSSZGH1JIIX3MNCsqLTD9o1Oj6ebnnDfvvefezrnbdCHhCw/n433P8z7nPe/hBEEAtX0U7hc164uwuvVSXKwZLoOmaRDim+7m9vZa0WiEKSUFFpNpCWlmMyypqTDRuYn6t3k8vmQ2gRDCxs0t9fW45F52aBTROJLtZl7nEZad2m+KtoQCQ0FBARyOCGRZ/q92I1WgqqXlfdd95VsrK8/pChIEqqpCkiQsiCII0aBQZZoWl8lzFDwsFjMl0DBLY8Lj41hBwK4jSQrWOIphL6xYyhwJDWGo6wFSaH1Y3PTCAsITE1oyAa8flhWkbSiCLX8vun11eiGIpiJ/z2nYdx5HqLdVV7elrOzsuqysL3rmBIGiKPizKCHHWY4PLVeQbnXAdegqdhy+hu8dDTBnbqQJZJ1A7u+vz7RaiymWCZgCRSF6Edk8b9cx+B/W6WuVxPaZnyiqXoPpyUmVYvkKTIFClHigEieKjYuSvETUllaF4GAUM1NT6ooaJDKx+aDfC9fByxj90REb+9ppmIoAscH/6leg8MS9DJXPAM9xHCM443K57C6biMjcHDaVVCHw9RmCA2/RGC5C00AqXk/m4p20HZK4CM/J3Zk9n0ecMBhDQnJHcrTisyMfdQXOilrdMfxcwoHq/fg5R59TiQV3hYGKo6X2J/c7LyQIjOx9GXhOw/zoJ8wEevRGyp53o/lGMNYsBgPtEwLecwov7/jGDKa1twT6o3KpL4MdZgGsWZLtfPr7f1q58k1JNHy7YYaM+J+K3Y2PmAIbRavX66229hrGVvvL5uzsHDEUvUu+NT1my78CDAAMK1a8/QaZCgAAAABJRU5ErkJggg=="><br />
<input type="submit" name="submit" value="Product Update"><br>

</form>
<hr>
<b>53. Product delete {adminproductdelete.php}</b>
<form name="login" method="POST" action="adminproductdelete.php">
<br>
Product ID : <input type="text" name="id" placeholder="Product Id">
<input type="submit" name="submit" value="Submit"><br>

</form>
<hr>
<b>54. Getpendingorders{adminpendingorder.php}</b>
<form name="login" method="POST" action="adminpendingorder.php">
<br>
name: <input type="text" name="name">
<input type="submit" name="submit" value="Search"><br>

</form>
<hr>
<b>55. Remove Product Image{adminremoveproimage.php}</b>
<form name="login" method="POST" action="adminremoveproimage.php">
<br>
Product Id: <input type="text" name="proid">
File Path: <input type="text" name="file">
<input type="submit" name="submit" value="Search"><br>

</form>
<hr>
</body>

</html>
