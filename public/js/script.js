// Helper function to safely set input values
function setValue(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.value = value || '';
    }
}

// Add event listener to search button
document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('searchComplaintBtn');
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            // Get the complaint number from the input field
            const complaintInput = document.getElementById('complaint_number');
            const complaintNo = complaintInput ? complaintInput.value.trim() : '';

            if (!complaintNo) {
                alert('Please enter a complaint number');
                return;
            }

            // Show loading state
            searchButton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            searchButton.disabled = true;

            // Clear previous data
            setValue('job_number', '');
            setValue('coms_complaint_date', '');
            setValue('job_type', '');
            setValue('customer_name', '');
            setValue('contact_no', '');
            setValue('technician_name', '');
            setValue('purchase_date', '');
            setValue('product', '');
            setValue('job_status', '');
            setValue('problem', '');
            setValue('workdone', '');

            // Create FormData and append the complaint number
            const formData = new FormData();
            formData.append('complaint_number', complaintNo);

            fetch('/fetch-coms-data', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Received COMS data:', data);

                if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    // Populate form fields with API data
                    setValue('job_number', data.JobNo || '');
                    setValue('coms_complaint_date', data.COMSComplaintDate ? data.COMSComplaintDate.split('T')[0] : '');
                    setValue('job_type', data.JobType || '');
                    setValue('customer_name', data.CustomerName || '');
                    setValue('contact_no', data.ContactNo || '');
                    setValue('technician_name', data.TechnicianName || '');
                    setValue('purchase_date', data.DateofPurchase ? data.DateofPurchase.split('T')[0] : '');
                    setValue('product', data.Product || '');
                    setValue('job_status', data.JobStatus || '');
                    setValue('problem', data.Problem || '');
                    setValue('workdone', data.WorkDone || '');
                }
            })
            .catch(error => {
                console.error('Error fetching COMS data:', error);
                alert('Error connecting to server');
            })
            .finally(() => {
                // Reset button state
                searchButton.innerHTML = '<i class="bi bi-search"></i>';
                searchButton.disabled = false;
            });
        });
    }
});
