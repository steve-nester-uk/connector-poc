<?php
//workflow 1: send request and parse response (which could include a redirect) (3d CC case)
//workflow 2: send request and always redirect to PP with some response data (Klarna case)
//workflow 3: always redirect to PP (Iyzico case)
class Workflow {
	const Normal = 1;
	const RequestAndAlwaysRedirectThenExternal = 2;
	const AlwaysRedirectThenExternal = 3;
}
?>