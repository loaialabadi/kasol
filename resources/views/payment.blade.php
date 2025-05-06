<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="#">
    <title>Row Best | Payment Successful</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        .success-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .success-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 30px;
            width: 400px;
        }
        .success-icon {
            font-size: 50px;
            <?php if($order->payment_status == 'accepted'){ ?>
                color: #4caf50;
            <?php } else { ?>
                color:rgb(241, 64, 64);
            <?php } ?>
        }
        .success-message {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="success-container">
        <div class="success-card">
            @if($order->payment_status == 'accepted')
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="success-message">Payment successful</div>
            @else
                <div class="success-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="success-message">Payment failed</div>
            @endif
            <a href="{{ $url }}" class="btn mt-3"><i class="fas fa-angle-double-right"></i></a>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
