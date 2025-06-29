<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Test Offer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Send Test Offer</h3>
                    </div>
                    <div class="card-body">
                        <p>Click the button below to send a test offer message to your account:</p>
                        <div id="result" class="alert d-none"></div>
                        <button id="sendOfferBtn" class="btn btn-primary">Send Test Offer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sendOfferBtn').addEventListener('click', function() {
            // Disable button during request
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            
            const resultDiv = document.getElementById('result');
            
            fetch('send_test_offer.php')
                .then(response => response.json())
                .then(data => {
                    resultDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
                    
                    if (data.success) {
                        resultDiv.classList.add('alert-success');
                        resultDiv.innerHTML = 'Success! ' + data.message + '<br>Go to <a href="client-panel/messages.php">your messages</a> to see the offer.';
                    } else {
                        resultDiv.classList.add('alert-danger');
                        resultDiv.innerHTML = 'Error: ' + data.message;
                    }
                })
                .catch(error => {
                    resultDiv.classList.remove('d-none');
                    resultDiv.classList.add('alert-danger');
                    resultDiv.innerHTML = 'Error: ' + error.message;
                })
                .finally(() => {
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = 'Send Test Offer';
                });
        });
    </script>
</body>
</html>