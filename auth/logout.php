<?php
session_start(); 
session_destroy();
header("Location: http://localhost/tourism%20agency/main/");
exit();