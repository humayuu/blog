<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Verify Your Email</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">

    <!-- Preheader -->
    <div
        style="display:none; font-size:1px; color:#f4f7fb; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">
        Your verification code is ready. Complete your email verification in minutes.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background-color:#f4f7fb; padding:35px 12px;">
        <tr>
            <td align="center">

                <!-- Email Card -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:520px; background-color:#ffffff; border-radius:16px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#2563eb; padding:30px 24px;">
                            <div
                                style="font-size:13px; font-weight:bold; letter-spacing:2px; color:#dbeafe; text-transform:uppercase;">
                                Email Verification
                            </div>

                            <h1
                                style="margin:12px 0 0; font-size:27px; line-height:1.3; color:#ffffff; font-weight:700;">
                                Verify Your Email
                            </h1>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding:34px 28px 24px;">

                            <p style="margin:0 0 14px; font-size:16px; line-height:1.7; color:#374151;">
                                Hi <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="margin:0 0 24px; font-size:15px; line-height:1.8; color:#6b7280;">
                                Welcome! We're happy to have you with us.
                                Please use the verification code below to verify your email address and complete your
                                registration.
                            </p>

                            <!-- OTP Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center"
                                        style="background-color:#eff6ff; border:1px solid #dbeafe; border-radius:12px; padding:24px 12px;">

                                        <p
                                            style="margin:0 0 12px; font-size:12px; font-weight:bold; letter-spacing:2px; color:#2563eb; text-transform:uppercase;">
                                            Your Verification Code
                                        </p>

                                        <div
                                            style="font-size:34px; line-height:1.4; font-weight:700; letter-spacing:8px; color:#1d4ed8; overflow-wrap:anywhere;">
                                            {{ $otp }}
                                        </div>

                                        <p style="margin:12px 0 0; font-size:13px; color:#64748b;">
                                            This code expires in <strong>10 minutes</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-top:24px;">
                                <tr>
                                    <td width="28" valign="top" style="font-size:18px; padding-top:2px;">
                                        🔒
                                    </td>
                                    <td style="font-size:13px; line-height:1.8; color:#6b7280;">
                                        <strong style="color:#374151;">Keep your code private.</strong>
                                        Never share this verification code with anyone.
                                        Our team will never ask you to share it.
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:25px 0 0; font-size:14px; line-height:1.8; color:#6b7280;">
                                Didn't create an account? You can safely ignore this email.
                            </p>

                            <p style="margin:20px 0 0; font-size:15px; line-height:1.7; color:#374151;">
                                Best regards,<br>
                                <strong style="color:#2563eb;">The Support Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="background-color:#f9fafb; border-top:1px solid #e5e7eb; padding:22px 24px;">

                            <p style="margin:0; font-size:12px; line-height:1.8; color:#9ca3af;">
                                This is an automated email. Please do not reply to this message.
                            </p>

                            <p style="margin:8px 0 0; font-size:12px; color:#9ca3af;">
                                &copy; {{ date('Y') }} Your App. All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

                <p
                    style="max-width:520px; margin:18px 0 0; font-size:11px; line-height:1.6; text-align:center; color:#9ca3af;">
                    This message was sent to you because an email verification was requested for your account.
                </p>

            </td>
        </tr>
    </table>

</body>

</html>
