<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f5f9; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f3f5f9; padding:40px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:14px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#172554; padding:30px 24px;">
                            <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700; letter-spacing:0.3px;">
                                Account Security
                            </h1>
                            <p style="margin:8px 0 0; color:#cbd5e1; font-size:14px; line-height:22px;">
                                Password recovery request
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding:36px 32px 24px;">

                            <p style="margin:0 0 16px; font-size:16px; line-height:25px;">
                                Hello <strong>{{ $userName }}</strong>,
                            </p>

                            <h2 style="margin:0 0 16px; color:#111827; font-size:23px; line-height:31px;">
                                Reset your password
                            </h2>

                            <p style="margin:0 0 24px; color:#4b5563; font-size:15px; line-height:25px;">
                                We received a request to reset the password for your account.
                                Click the button below to choose a new password.
                            </p>

                            <!-- Reset Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td align="center" bgcolor="#2563eb" style="border-radius:8px;">
                                        <a href="{{ $resetUrl }}" target="_blank"
                                            style="display:inline-block; padding:15px 32px; color:#ffffff; font-size:15px; font-weight:700; text-decoration:none; border-radius:8px;">
                                            Reset My Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 10px; color:#6b7280; font-size:13px; line-height:21px;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>

                            <p style="margin:0 0 24px; font-size:13px; line-height:21px; word-break:break-all;">
                                <a href="{{ $resetUrl }}" style="color:#2563eb; text-decoration:underline;">
                                    {{ $resetUrl }}
                                </a>
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="background-color:#f9fafb; border-left:4px solid #2563eb; padding:14px 16px;">
                                        <p style="margin:0; color:#4b5563; font-size:13px; line-height:21px;">
                                            <strong style="color:#111827;">Security notice:</strong>
                                            If you did not request a password reset, you can safely ignore
                                            this email. Your password will remain unchanged.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="padding:22px 24px; background-color:#f9fafb; border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 6px; color:#6b7280; font-size:12px; line-height:20px;">
                                This is an automated security email. Please do not reply.
                            </p>
                            <p style="margin:0; color:#9ca3af; font-size:12px; line-height:20px;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
