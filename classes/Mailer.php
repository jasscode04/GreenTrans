<?php
/**
 * GreenTrans - Mailer Class using PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once ROOT_PATH . 'PHPMailer/src/Exception.php';
require_once ROOT_PATH . 'PHPMailer/src/PHPMailer.php';
require_once ROOT_PATH . 'PHPMailer/src/SMTP.php';

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // SMTP Configuration
        // You can move these to config/config.php later
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com'; // Change to your SMTP host
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'codecpp019@gmail.com'; // Your email
        $this->mail->Password   = 'spfj cfvv yycf apmw'; // Your app password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        
        $this->mail->setFrom('codecpp019@gmail.com', 'GreenTrans Support');
    }

    public function sendOTP($email, $otp, $name = '') {
        try {
            $this->mail->addAddress($email, $name);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Verify your GreenTrans Account';
            
            $this->mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #10b981;'>GreenTrans Verification</h2>
                    <p>Hello $name,</p>
                    <p>Your One-Time Password (OTP) for account verification is:</p>
                    <div style='background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; text-align: center; letter-spacing: 5px; border-radius: 5px;'>
                        $otp
                    </div>
                    <p>This OTP is valid for 10 minutes. Please do not share it with anyone.</p>
                    <p>Regards,<br>Team GreenTrans</p>
                </div>
            ";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendPasswordReset($email, $otp, $name = '') {
        try {
            $this->mail->addAddress($email, $name);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Reset your GreenTrans Password';
            
            $this->mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #ef4444;'>Password Reset Request</h2>
                    <p>Hello $name,</p>
                    <p>We received a request to reset your password. Use the following OTP to proceed:</p>
                    <div style='background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; text-align: center; letter-spacing: 5px; border-radius: 5px;'>
                        $otp
                    </div>
                    <p>If you did not request this, please ignore this email.</p>
                    <p>Regards,<br>Team GreenTrans</p>
                </div>
            ";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}
