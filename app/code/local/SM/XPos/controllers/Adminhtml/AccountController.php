<?php
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'CustomerController.php';
class SM_XPos_Adminhtml_AccountController extends Mage_Adminhtml_CustomerController
{
    public function updateAction()
    {
        $response = array(
            'message' => ''
        );

        $data = $this->getRequest()->getPost('account');
        if ($data) {

            $customerId = (int) $this->getRequest()->getParam('customer_id');
            $customer = Mage::getModel('customer/customer');

            if ($customerId) {
                $customer->load($customerId);
            }

            foreach($data as $key => $value){
                if($value){
                    $customer->setData($key, $value);
                }
            }

            try {

                $customer->save();
                $response['message'] = $this->__("Customer's information has been updated! ");

            } catch (Mage_Core_Exception $e) {
                $response['message'] .= '<div>'. $this->__($e->getMessage()) .'</div>';
                return $this->getResponse()->setBody(json_encode($response));
            } catch (Exception $e) {
                $response['message'] .= '<div>'. Mage::helper('adminhtml')->__('An error occurred while saving the customer.') .'</div>';
                return $this->getResponse()->setBody(json_encode($response));
            }
        }
        return $this->getResponse()->setBody(json_encode($response));
    }

    public function updateAddressAction()
    {
        $response = array(
            'message' => ''
        );
        // Save data
        if ($this->getRequest()->isPost()) {
            $customerId = $this->getRequest()->getPost('customer_id');
            $customer = Mage::getModel('customer/customer')->load($customerId);
            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getPost('id');
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->setId($existsAddress->getId());
                }
            }

            $errors = array();

            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntity($address);
            $addressData    = $addressForm->extractData($this->getRequest());
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                $errors = $addressErrors;
            }

            try {
                $addressForm->compactData($addressData);
                $address->setCustomerId($customer->getId())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));

                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }

                if (count($errors) === 0) {
                    $address->save();
                    $response['message'] = ($this->__('The address has been saved.'));
                    return $this->getResponse()->setBody(json_encode($response));
                } else {
                    foreach ($errors as $errorMessage) {
                        $response['message'] .= $errorMessage . '<br/>';
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $response['message'] = $e->getMessage();
            } catch (Exception $e) {
                $response['message'] = $this->__('Cannot save address.');
            }
        }

        return $this->getResponse()->setBody(json_encode($response));
    }
}