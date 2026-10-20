<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Blog</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: Arial, sans-serif;">

    <div
        style="max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

        <!-- Header -->
        <div style="background-color: #2563eb; padding: 30px 20px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 28px;">
                Welcome to Our Blog!
            </h1>
            <p style="margin: 10px 0 0; font-size: 15px;">
                We're happy to have you here!
            </p>
        </div>

        <!-- Email Content -->
        <div style="padding: 30px; color: #333333; line-height: 1.7;">

            <h2 style="color: #2563eb; margin-top: 0;">
                Hello, {{ $user->name }}!
            </h2>

            <p>
                Welcome to our blog!
            </p>

            <p>
                We are delighted to have you as a member of our blogging
                community. Your account has been successfully created.
                You can now log in and explore our blog.
            </p>

            <!-- Credentials Box -->
            <div
                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">

                <h3 style="margin-top: 0; color: #1e293b;">
                    Your Login Credentials
                </h3>

                <p style="margin: 10px 0;">
                    <strong>Email:</strong><br>
                    <span style="color: #475569;">
                        {{ $user->email }}
                    </span>
                </p>

                <p style="margin: 10px 0;">
                    <strong>Password:</strong><br>
                    <span style="color: #475569;">
                        {{ $password }}
                    </span>
                </p>

            </div>

            <!-- Security Notice -->
            <div style="background-color: #fff7ed; border-left: 4px solid #f97316; padding: 15px; margin: 20px 0;">
                <strong style="color: #c2410c;">
                    Important Security Notice
                </strong>

                <p style="margin: 8px 0 0; color: #7c2d12;">
                    For security reasons, please log in to your account
                    and change your password immediately after your
                    first login.
                </p>
            </div>

            <!-- Login Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}"
                    style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: bold;">
                    Login to Your Account
                </a>
            </div>

            <p>
                If you have any questions or need assistance, please
                feel free to contact us.
            </p>

            <p>
                Thank you for joining our community!
            </p>

            <p style="margin-bottom: 0;">
                Best regards,<br>
                <strong style="color: #2563eb;">The Blog Team</strong>
            </p>

        </div>

        <!-- Footer -->
        <div style="background-color: #f1f5f9; padding: 20px; text-align: center; color: #64748b; font-size: 12px;">
            <p style="margin: 0;">
                &copy; {{ date('Y') }} Our Blog. All rights reserved.
            </p>
            <p style="margin: 8px 0 0;">
                This is an automated welcome email. Please do not reply.
            </p>
        </div>

    </div>

</body>

</html>
