<style>
    .form-container {
        background-color: #1a2c3c;
        color: #fff;
        padding: 30px;
        border-radius: 8px;
        max-width: 600px;
        margin: 0 auto;
    }
    .btn-submit {
        background-color: #fa5b3d;
        color: #fff;
    }
</style>
<div class="container mt-2 mb-2">
    <div class="form-container">
        <h2 class="text-center mb-4 text-white">Fill the details below to get the brochure.</h2>
        <form id="brochureForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="purpose" class="form-label">Purpose *</label>
                    <select class="form-control" id="purpose" required>
                        <option value="Enquiry">Enquiry</option>
                        <option value="Feedback">Feedback</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label">Location *</label>
                    <select class="form-control" id="location" required>
                        <option value="Location 1">Location 1</option>
                        <option value="Location 2">Location 2</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone *</label>
                    <input type="tel" class="form-control" id="phone" pattern="[0-9]{10}" required title="Please enter a 10-digit phone number">
                </div>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="disclaimer" required>
                <label class="form-check-label" for="disclaimer">I authorize Nirvana Homespaces LLP and its representatives to Call, SMS, Email or WhatsApp me about its updates and notifications. This consent overrides any registration for DND / NDNC.</label>
            </div>

            <button type="submit" class="btn btn-submit w-100">Submit</button>
        </form>
    </div>
</div>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
$(document).ready(function() {
    $('#brochureForm').on('submit', function(e) {
        console.log('Form submitted'); // Debugging line
        e.preventDefault(); // Prevent default form submission

        const formData = {
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            phone: $('#phone').val().trim(),
            purpose: $('#purpose').val(),
            location: $('#location').val(),
            disclaimer: $('#disclaimer').is(':checked')
        };

        $.ajax({
            url: '<?= base_url('public/contactfetch')?>', // <-- Replace with your actual endpoint
            method: 'POST',
            // contentType: 'application/json',
            data: formData,
            success: function(response) {
                console.log('Response:', response); // Debugging line
                alert('Form submitted successfully!');
                $('#brochureForm')[0].reset(); // Reset form
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', errorThrown);
                alert('Submission failed. Please try again.');
            }
        });
    });
});
</script>