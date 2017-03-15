<div class="container">
	<div>
    <ol class="breadcrumb">
      <li class="active">Home</li>
    </ol>
  </div>
	<!--Request:-->
	<!--4200000000000000-->
	<!--4111111111111111-->
	<input type="checkbox" name="requestXML" id="requestXML" value="true" checked="checked" /> Send in requestXML?
	<!-- <textarea id="request">xml.version=1.0
xml.encoding=UTF-8
Request.version=1.0
Request.Transaction.mode=TEST
Request.Transaction.requestTimestamp=2008-12-01 11:37:47
Request.Transaction.Identification.ShortID=2894.9340.3418
Request.Transaction.Identification.UUID=9JoCmAU0K2XAeBTXpqY3vJlIpYxECq8B
Request.Transaction.MerchantAccount.type=NEXT
Request.Transaction.MerchantAccount.MerchantID=8a8294174b7ecb28014b9699220015ca
Request.Transaction.MerchantAccount.MerchantName=8a8294174b7ecb28014b9699220015cc
Request.Transaction.MerchantAccount.Password=sy6KJsT8
Request.Transaction.MerchantAccount.Country=DE
Request.Transaction.Payment.type=PA
Request.Transaction.Payment.Amount=300
Request.Transaction.Payment.Currency=GBP
Request.Transaction.Payment.Descriptor=2894.9340.3418 TPX_order# PSP_A/MER_A/DEFAULT
Request.Transaction.CreditCardAccount.Holder=Jane Jones
Request.Transaction.CreditCardAccount.Verification=123
Request.Transaction.CreditCardAccount.Brand=VISA
Request.Transaction.CreditCardAccount.Number=4111111111111111
Request.Transaction.CreditCardAccount.Expiry.month=05
Request.Transaction.CreditCardAccount.Expiry.year=2018
Request.Transaction.Customer.Name.Given=John
Request.Transaction.Customer.Name.Family=Doe
Request.Transaction.Customer.Contact.Ip=101.202.011.022
Request.Transaction.Customer.Contact.Email=john@doe.com
Request.Transaction.Customer.Contact.Phone=333444555
Request.Transaction.Customer.Contact.Mobile=0049 177 6542123
Request.Transaction.Customer.Address.Country=GB
Request.Transaction.Customer.Address.City=London
Request.Transaction.Customer.Address.State=CA
Request.Transaction.Customer.Address.Street=Lombard St 10
Request.Transaction.Customer.Address.Street2=Apt 214
Request.Transaction.Customer.Address.Zip=AB1 2DE
Request.Transaction.Authentication.Eci=05
Request.Transaction.Authentication.Verification=AAACAgSRBklmQCFgMpEGAAAAAAA=
Request.Transaction.Authentication.Xid=CAACCVVUlwCXUyhQNlSXAAAAAAA=
Request.Transaction.Parameters.Parameter.OPP_cart.items[1].name=product 1
Request.Transaction.Parameters.Parameter.OPP_cart.items[1].type=physical
Request.Transaction.Parameters.Parameter.OPP_cart.items[1].quantity=1
Request.Transaction.Parameters.Parameter.OPP_cart.items[1].price=100
Request.Transaction.Parameters.Parameter.OPP_cart.items[1].unit_price=100
Request.Transaction.Parameters.Parameter.OPP_cart.items[1].description=vala
Request.Transaction.Parameters.Parameter.OPP_cart.items[2].name=product 2
Request.Transaction.Parameters.Parameter.OPP_cart.items[2].type=physical
Request.Transaction.Parameters.Parameter.OPP_cart.items[2].quantity=1
Request.Transaction.Parameters.Parameter.OPP_cart.items[2].price=200
Request.Transaction.Parameters.Parameter.OPP_cart.items[2].unit_price=200
Request.Transaction.Parameters.Parameter.OPP_cart.items[2].description=vala</textarea> -->
<textarea id="request">&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;Request version="1.0">
 &lt;Transaction mode="TEST" requestTimestamp="2008-12-01 11:37:47">
 &lt;Identification>
 &lt;ShortID>2894.9340.3418&lt;/ShortID>
 &lt;UUID>MGQTluFAfgvoBpOOs8Uc2eKmj96lFRrC&lt;/UUID>
 &lt;/Identification>
 &lt;MerchantAccount type="NEXT">
 &lt;MerchantID>8a8294174b7ecb28014b9699220015ca&lt;/MerchantID>
 &lt;MerchantName>8a8294174b7ecb28014b9699220015cc&lt;/MerchantName>
 &lt;Password>sy6KJsT8&lt;/Password>
 &lt;Country>DE&lt;/Country>
 &lt;/MerchantAccount>
 &lt;Payment type="PA">
 &lt;Amount>37.00&lt;/Amount>
 &lt;Currency>EUR&lt;/Currency>
 &lt;Descriptor>2894.9340.3418 TPX_order# PSP_A/MER_A/DEFAULT&lt;/Descriptor>
 &lt;/Payment>
 &lt;CreditCardAccount>
 &lt;Holder>jean le coq&lt;/Holder>
 &lt;Verification>123&lt;/Verification>
 &lt;Brand>VISA&lt;/Brand>
 &lt;Number>4200000000000000&lt;/Number>
 &lt;Expiry month="01" year="2018" />
 &lt;/CreditCardAccount>
 &lt;Customer>
 &lt;Name>
 &lt;Family>kosel&lt;/Family>
 &lt;Given>bobby&lt;/Given>
 &lt;/Name>
 &lt;Contact>
 &lt;Ip>101.202.011.022&lt;/Ip>
 &lt;Email>bob_kosel@mailserver.com&lt;/Email>
 &lt;Phone>0049 89 6542123&lt;/Phone>
 &lt;Mobile>0049 177 6542123&lt;/Mobile>
 &lt;/Contact>
 &lt;Address>
 &lt;Country>DE&lt;/Country>
 &lt;City>Frankfurt&lt;/City>
 &lt;State>DE7&lt;/State>
 &lt;Street>Hauptstrasse&lt;/Street>
 &lt;Zip>61821&lt;/Zip>
 &lt;/Address>
 &lt;/Customer>
 &lt;Authentication>
 &lt;Eci>05&lt;/Eci>
 &lt;Verification>AAACAgSRBklmQCFgMpEGAAAAAAA=&lt;/Verification>
 &lt;Xid>CAACCVVUlwCXUyhQNlSXAAAAAAA=&lt;/Xid>
 &lt;/Authentication>
 &lt;/Transaction>
&lt;/Request>
	</textarea>
		Response:
	<textarea id="response"></textarea>
	<div id="redirectArea"></div>
	<button id="btn-submit">Send</button>
</div>
