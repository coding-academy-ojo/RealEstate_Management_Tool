<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Welcome to Orange Real Estate</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto;">
        <tr>
            <td style="padding: 24px 0; text-align: center;">
                <h1 style="margin: 0; color: #ff6f00;">Orange Real Estate</h1>
            </td>
        </tr>
        <tr>
            <td style="background-color: #ffffff; padding: 24px; border: 1px solid #eee; border-radius: 8px;">
                <p style="margin-top: 0;">Hello {{ $user->name }},</p>

                <p>Welcome to the Orange Real Estate platform. Your account has been created and you can now sign in
                    using the credentials below:</p>

                <table cellpadding="0" cellspacing="0" style="width: 100%; margin: 16px 0;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; width: 160px;">Login URL</td>
                        <td style="padding: 8px 0;"><a href="{{ $loginUrl }}"
                                style="color: #ff6f00;">{{ $loginUrl }}</a></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Email</td>
                        <td style="padding: 8px 0;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Temporary Password</td>
                        <td style="padding: 8px 0;">{{ $password }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Role</td>
                        <td style="padding: 8px 0;">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td>
                    </tr>
                    @if (!empty($friendlyPrivileges))
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Privileges</td>
                            <td style="padding: 8px 0;">
                                <ul style="padding-left: 18px; margin: 0;">
                                    @foreach ($friendlyPrivileges as $privilege)
                                        <li>{{ $privilege }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endif
                </table>

                <p style="margin-bottom: 16px;">For security, please sign in and change your password on your first
                    visit.</p>

                <p style="margin-bottom: 0;">Best regards,<br>Orange Real Estate Team</p>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; color: #999; font-size: 12px; padding: 16px 0;">
                (c) {{ date('Y') }} Orange Real Estate. All rights reserved.
            </td>
        </tr>
    </table>
</body>

</html>
