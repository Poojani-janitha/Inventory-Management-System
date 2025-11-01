<?php
$errors = array();

/*--------------------------------------------------------------*/
/* Escape special characters for SQL */
function real_escape($str){
    global $con;
    return mysqli_real_escape_string($con, $str);
}

/*--------------------------------------------------------------*/
/* Remove HTML characters */
function remove_junk($str){
    $str = nl2br($str);
    $str = htmlspecialchars(strip_tags($str), ENT_QUOTES);
    return $str;
}

/*--------------------------------------------------------------*/
/* Uppercase first character */
function first_character($str){
    $val = str_replace('-', " ", $str);
    return ucfirst($val);
}

/*--------------------------------------------------------------*/
/* Check input fields not empty */
function validate_fields($var){
    global $errors;
    foreach ($var as $field) {
        $val = remove_junk($_POST[$field] ?? '');
        if($val == ''){
            $errors[] = $field ." can't be blank.";
        }
    }
}

/*--------------------------------------------------------------*/
/* Display session messages */
function display_msg($msg = array()){
    $output = '';
    if (!empty($msg)) {
        foreach ($msg as $key => $value) {
            $output .= "<div class=\"alert alert-{$key}\">";
            $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
            $output .= remove_junk($value);
            $output .= "</div>";
        }
    }
    return $output;
}

/*--------------------------------------------------------------*/
/* Redirect to another page */
function redirect($url, $permanent = false){
    if (!headers_sent()) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
    }
    exit();
}

/*--------------------------------------------------------------*/
/* Count ID (used for table rows) */
function count_id(){
    static $count = 1;
    return $count++;
}

/* Create readable date */
function read_date($str){
    if($str)
        return date('F j, Y, g:i:s a', strtotime($str));
    else
        return null;
}

/*--------------------------------------------------------------*/
/* Create current datetime */
function make_date(){
    return strftime("%Y-%m-%d %H:%M:%S", time());
}

/*--------------------------------------------------------------*/
/* Total sale, buying price, and profit */
function total_price($totals){
    $sum = 0;
    $sub = 0;
    foreach($totals as $total ){
        $sum += $total['total_saleing_price'];
        $sub += $total['total_buying_price'];
    }
    $profit = $sum - $sub;
    return array($sum, $profit);
}

/*--------------------------------------------------------------*/
/* Generate random string */
function randString($length = 5){
    $str = '';
    $cha = "0123456789abcdefghijklmnopqrstuvwxyz";
    for($x = 0; $x < $length; $x++){
        $str .= $cha[mt_rand(0, strlen($cha)-1)];
    }
    return $str;
}

/*--------------------------------------------------------------*/
/* Send email function - REPLACE THE OLD ONE WITH THIS */
function send_email($to, $subject, $message, $from_email = "nuwaniprabhashi2003@gmail.com") {
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Inventory System <" . $from_email . ">" . "\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";

    return mail($to, $subject, $message, $headers);
}

/*--------------------------------------------------------------*/
/* Send return notification email function - for add_return.php */
function send_return_email($to, $subject, $message) {
    $from_email = "nimharachalana12@gmail.com";
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Inventory Management System <" . $from_email . ">" . "\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    // Clear any previous errors
    error_clear_last();
    
    // Try to send email
    $result = @mail($to, $subject, $message, $headers);
    
    // If failed, log the error for debugging
    if(!$result){
        $error = error_get_last();
        if($error){
            error_log("Email sending failed to {$to}: " . $error['message']);
        }
        
        // Check php.ini mail configuration
        $smtp = ini_get('SMTP');
        $smtp_port = ini_get('smtp_port');
        $sendmail_from = ini_get('sendmail_from');
        
        if(empty($smtp)){
            error_log("Email config: SMTP not configured in php.ini");
        } else {
            error_log("Email config: SMTP={$smtp}, Port={$smtp_port}, From={$sendmail_from}");
        }
    }
    
    return $result;
}
?>
