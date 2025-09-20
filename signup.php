<?php

session_start();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $country = trim($_POST['country']);
    $currency = trim($_POST['currency']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('SELECT id FROM users WHERE user_name = ?');
    $stmt->bind_param('s', $user_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        $stmt = $conn->prepare('INSERT INTO users (user_name, email, full_name, password, country, currency) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $user_name, $email, $full_name, $hashed_password, $country, $currency);
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $user_name;
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Error creating account!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updateCurrency() {
            var country = document.getElementById('country').value;
            var currencyMap = <?php echo json_encode($country_currency); ?>;
            document.getElementById('currency').value = currencyMap[country] || '';
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Sign Up</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="user_name" class="form-label">Username</label>
            <input type="text" class="form-control" id="user_name" name="user_name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
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
        <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>
    <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>