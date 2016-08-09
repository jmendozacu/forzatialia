<?php
class Inchoo_SimpleContact_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Get current layout state
        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'inchoo.simple_contact',
            array(
                'template' => 'inchoo/simple_contact.phtml'
            )
        );
        $this->getLayout()->getBlock('content')->append($block);
        //$this->getLayout()->getBlock('right')->insert($block, 'catalog.compare.sidebar', true);
        $this->_initLayoutMessages('core/session');
        $this->renderLayout();
    }
    public function sendemailAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();
        $mail = new Zend_Mail();
	/*	$message= array(
        "Order Id" => $params['id'],
        "Name" =>  $params['name'],
	"Email" => $params['email'],
	"Phone" => $params['phone'],
	"Reason for Return" => $params['comment'],
	);*/
	$text = ' <b>Order ID: </b>'.$params['id'].'<br /> <b>Name: </b>'.$params['name'].'<br /> <b>Email:</b> '.$params['email'].'<br /> <b>Phone: </b>'.$params['phone'].'<br /> <b>Reason For Return: </b>'.$params['comment'];
	//$text="ID: ".$params['id']." Name: ".$params['name']." Email: ".$params['email']." Phone: ".$params['phone']." Reason For Return: ".$params['comment'];
        //$mail->setBodyText(implode(', ',$text));
		  $mail->setBodyHtml($text);
		 //$mail->setBodyText($params['name'], $params['email'], $params['phone'], $params['comment']);
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo('info@forzaitalia.com.au', 'Some Recipient');
        $mail->setSubject('Returns & Exchanges | Forza Italia ');
        try {
            $mail->send();
        }
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('Unable to send email. Sample of a custom notification error from Inchoo_SimpleContact.');
        }
		  Mage::getSingleton('core/session')->addSuccess("We have received your return request and one of our friendly staff will be in contact with you shortly. ");
 
 
         session_write_close(); //THIS LINE IS VERY IMPORTANT!
        //Redirect back to index action of (this) inchoo-simplecontact controller
        $this->_redirect('inchoo-simplecontact/');
		
    }
}
?>