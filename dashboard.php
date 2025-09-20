<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
require_once 'db_connect.php';

$country_currency = [
      'Afghanistan' => 'AFN',
'Albania' => 'ALL',
'Algeria' => 'DZD',
'Andorra' => 'EUR',
'Angola' => 'AOA',
'Antigua and Barbuda' => 'XCD',
'Argentina' => 'ARS',
'Armenia' => 'AMD',
'Aruba' => 'AWG',
'Australia' => 'AUD',
'Austria' => 'EUR',
'Azerbaijan' => 'AZN',
'Bahamas' => 'BSD',
'Bahrain' => 'BHD',
'Bangladesh' => 'BDT',
'Barbados' => 'BBD',
'Belarus' => 'BYN',
'Belgium' => 'EUR',
'Belize' => 'BZD',
'Benin' => 'XOF',
'Bhutan' => 'BTN',
'Bolivia' => 'BOB',
'Bosnia and Herzegovina' => 'BAM',
'Botswana' => 'BWP',
'Brazil' => 'BRL',
'Brunei' => 'BND',
'Bulgaria' => 'BGN',
'Burkina Faso' => 'XOF',
'Burundi' => 'BIF',
'Cabo Verde' => 'CVE',
'Cambodia' => 'KHR',
'Cameroon' => 'XAF',
'Canada' => 'CAD',
'Central African Republic' => 'XAF',
'Chad' => 'XAF',
'Chile' => 'CLP',
'China' => 'CNY',
'Colombia' => 'COP',
'Comoros' => 'KMF',
'Costa Rica' => 'CRC',
'Côte d\'Ivoire' => 'XOF',
'Croatia' => 'EUR',
'Cuba' => 'CUP',
'Cyprus' => 'EUR',
'Czech Republic' => 'CZK',
'Democratic Republic of the Congo' => 'CDF',
'Denmark' => 'DKK',
'Djibouti' => 'DJF',
'Dominica' => 'XCD',
'Dominican Republic' => 'DOP',
'Ecuador' => 'USD',
'Egypt' => 'EGP',
'El Salvador' => 'USD',
'Equatorial Guinea' => 'XAF',
'Eritrea' => 'ERN',
'Estonia' => 'EUR',
'Eswatini' => 'SZL',
'Ethiopia' => 'ETB',
'Fiji' => 'FJD',
'Finland' => 'EUR',
'France' => 'EUR',
'Gabon' => 'XAF',
'Gambia' => 'GMD',
'Georgia' => 'GEL',
'Germany' => 'EUR',
'Ghana' => 'GHS',
'Greece' => 'EUR',
'Grenada' => 'XCD',
'Guatemala' => 'GTQ',
'Guinea' => 'GNF',
'Guinea‑Bissau' => 'XOF',
'Guyana' => 'GYD',
'Haiti' => 'HTG',
'Honduras' => 'HNL',
'Hungary' => 'HUF',
'Iceland' => 'ISK',
'India' => 'INR',
'Indonesia' => 'IDR',
'Iran' => 'IRR',
'Iraq' => 'IQD',
'Ireland' => 'EUR',
'Israel' => 'ILS',
'Italy' => 'EUR',
'Jamaica' => 'JMD',
'Japan' => 'JPY',
'Jordan' => 'JOD',
'Kazakhstan' => 'KZT',
'Kenya' => 'KES',
'Kiribati' => 'AUD',
'Kuwait' => 'KWD',
'Kyrgyzstan' => 'KGS',
'Laos' => 'LAK',
'Latvia' => 'EUR',
'Lebanon' => 'LBP',
'Lesotho' => 'LSL',
'Liberia' => 'LRD',
'Libya' => 'LYD',
'Liechtenstein' => 'CHF',
'Lithuania' => 'EUR',
'Luxembourg' => 'EUR',
'Madagascar' => 'MGA',
'Malawi' => 'MWK',
'Malaysia' => 'MYR',
'Maldives' => 'MVR',
'Mali' => 'XOF',
'Malta' => 'EUR',
'Marshall Islands' => 'USD',
'Mauritania' => 'MRU',
'Mauritius' => 'MUR',
'Mexico' => 'MXN',
'Micronesia' => 'USD',
'Moldova' => 'MDL',
'Monaco' => 'EUR',
'Mongolia' => 'MNT',
'Montenegro' => 'EUR',
'Morocco' => 'MAD',
'Mozambique' => 'MZN',
'Myanmar' => 'MMK',
'Namibia' => 'NAD',
'Nauru' => 'AUD',
'Nepal' => 'NPR',
'Netherlands' => 'EUR',
'New Zealand' => 'NZD',
'Nicaragua' => 'NIO',
'Niger' => 'XOF',
'Nigeria' => 'NGN',
'North Korea' => 'KPW',
'North Macedonia' => 'MKD',
'Norway' => 'NOK',
'Oman' => 'OMR',
'Pakistan' => 'PKR',
'Palau' => 'USD',
'Panama' => 'PAB',
'Papua New Guinea' => 'PGK',
'Paraguay' => 'PYG',
'Peru' => 'PEN',
'Philippines' => 'PHP',
'Poland' => 'PLN',
'Portugal' => 'EUR',
'Qatar' => 'QAR',
'Republic of the Congo' => 'XAF',
'Romania' => 'RON',
'Russia' => 'RUB',
'Rwanda' => 'RWF',
'Saint Kitts and Nevis' => 'XCD',
'Saint Lucia' => 'XCD',
'Saint Vincent and the Grenadines' => 'XCD',
'Samoa' => 'WST',
'San Marino' => 'EUR',
'Sao Tome and Principe' => 'STN',
'Saudi Arabia' => 'SAR',
'Senegal' => 'XOF',
'Serbia' => 'RSD',
'Seychelles' => 'SCR',
'Sierra Leone' => 'SLL',
'Singapore' => 'SGD',
'Slovakia' => 'EUR',
'Slovenia' => 'EUR',
'Solomon Islands' => 'SBD',
'Somalia' => 'SOS',
'South Africa' => 'ZAR',
'South Korea' => 'KRW',
'South Sudan' => 'SSP',
'Spain' => 'EUR',
'Sri Lanka' => 'LKR',
'Sudan' => 'SDG',
'Suriname' => 'SRD',
'Sweden' => 'SEK',
'Switzerland' => 'CHF',
'Syria' => 'SYP',
'Taiwan' => 'TWD',
'Tajikistan' => 'TJS',
'Tanzania' => 'TZS',
'Thailand' => 'THB',
'Timor‑Leste' => 'USD',
'Togo' => 'XOF',
'Tonga' => 'TOP',
'Trinidad and Tobago' => 'TTD',
'Tunisia' => 'TND',
'Turkey' => 'TRY',
'Turkmenistan' => 'TMT',
'Tuvalu' => 'AUD',
'Uganda' => 'UGX',
'Ukraine' => 'UAH',
'United Arab Emirates' => 'AED',
'United Kingdom' => 'GBP',
'United States' => 'USD',
'Uruguay' => 'UYU',
'Uzbekistan' => 'UZS',
'Vanuatu' => 'VUV',
'Vatican City' => 'EUR',
'Venezuela' => 'VES',
'Vietnam' => 'VND',
'Yemen' => 'YER',
'Zambia' => 'ZMW',
'Zimbabwe' => 'ZWL',
];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'], $_POST['members'], $_POST['country'], $_POST['currency'])) {
    $group_name = trim($_POST['group_name']);
    $members = array_map('trim', explode(',', $_POST['members']));
    $creator = $_SESSION['user_name'];
    $country = trim($_POST['country']);
    $currency = trim($_POST['currency']);
    $stmt = $conn->prepare('INSERT INTO groups (group_name, creator, country, currency) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $group_name, $creator, $country, $currency);
    if ($stmt->execute()) {
        $group_id = $stmt->insert_id;

        $userStmt = $conn->prepare('SELECT user_name, email, full_name FROM users WHERE user_name = ?');
        $userStmt->bind_param('s', $creator);
        $userStmt->execute();
        $userStmt->bind_result($uname, $email, $full_name);
        if ($userStmt->fetch()) {
            $userStmt->close();
            $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name, email, full_name) VALUES (?, ?, ?, ?)');
            $stmt2->bind_param('isss', $group_id, $uname, $email, $full_name);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $userStmt->close();
        }
        foreach ($members as $member) {
            if ($member !== $creator && $member !== '') {
                $checkStmt = $conn->prepare('SELECT user_name, email, full_name FROM users WHERE user_name = ?');
                $checkStmt->bind_param('s', $member);
                $checkStmt->execute();
                $checkStmt->bind_result($uname, $email, $full_name);
                if ($checkStmt->fetch()) {
                    $checkStmt->close();
                    $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name, email, full_name) VALUES (?, ?, ?, ?)');
                    $stmt2->bind_param('isss', $group_id, $uname, $email, $full_name);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $checkStmt->close();
                }
            }
        }
        $message = 'Group created successfully!';
        header('Location: group.php');

    } else {
        $message = 'Error creating group.';
    }
}
?>
<!
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            padding: 32px;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            border: none;
        }
        .welcome {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 24px;
            color: #185a9d;
        }
        .member-list { margin-top: 10px; }
        .member-item { display: inline-block; background: #e3f2fd; padding: 5px 10px; border-radius: 12px; margin: 2px; }
        .remove-member { color: #d32f2f; cursor: pointer; margin-left: 8px; }
    </style>
    <script>
        function updateCurrency() {
            var country = document.getElementById('country').value;
            var currencyMap = <?php echo json_encode($country_currency); ?>;
            document.getElementById('currency').value = currencyMap[country] || '';
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</div>
        <?php if ($message): ?>
            <div class="alert alert-info"> <?php echo $message; ?> </div>
        <?php endif; ?>
        <h4>Create a Group</h4>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="group_name" class="form-label">Group Name</label>
                <input type="text" class="form-control" id="group_name" name="group_name" required>
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">Destination Country</label>
                <select class="form-select" id="country" name="country" onchange="updateCurrency()" required>
                    <option value="">Select Country</option>
                    <?php foreach ($country_currency as $country => $currency): ?>
                        <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <input type="text" class="form-control" id="currency" name="currency" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Add Members</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search_user" placeholder="Enter user name">
                    <button type="button" class="btn btn-outline-primary" id="add_member_btn">Add</button>
                </div>
                <div id="search_result" class="mt-2"></div>
                <div class="member-list" id="member_list"></div>
            </div>
            <input type="hidden" name="members" id="members_hidden">
            <button type="submit" class="btn btn-primary">Create Group</button>
        </form>
        <a href="group.php" class="btn btn-info mt-2">View My Groups & Invitations</a>
        <a href="logout.php" class="btn btn-outline-secondary mt-2">Logout</a>
    </div>
    <script>
        let members = [];
        $('#add_member_btn').click(function() {
            let user = $('#search_user').val().trim();
            if (!user || members.includes(user)) return;
            $.get('search_user.php', {user_name: user}, function(data) {
                if (data === 'found') {
                    members.push(user);
                    updateMemberList();
                    $('#search_result').html('<span class="text-success">User added!</span>');
                    $('#search_user').val('');
                } else {
                    $('#search_result').html('<span class="text-danger">User not found!</span>');
                }
            });
        });
        function updateMemberList() {
            $('#member_list').html('');
            members.forEach(function(user, idx) {
                $('#member_list').append(
                    `<span class="member-item">${user} <span class="remove-member" onclick="removeMember(${idx})">&times;</span></span>`
                );
            });
            $('#members_hidden').val(members.join(','));
        }
        window.removeMember = function(idx) {
            members.splice(idx, 1);
            updateMemberList();
        }
    </script>
</body>
</html>