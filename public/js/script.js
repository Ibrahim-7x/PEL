// Helper function to safely set input values
function setValue(id, value) {
    const element = document.getElementById(id);
    if (element) {
        // For date inputs, ensure we only set valid date strings
        if (element.type === 'date') {
            // Only set value if it's a valid date string (yyyy-mm-dd format)
            if (value && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
                element.value = value;
            } else {
                element.value = '';
            }
        } else {
            element.value = value || '';
        }
    }
}

// Helper function to extract date part from datetime strings
function extractDatePart(dateTimeString) {
    if (!dateTimeString) return '';
    let dateStr = dateTimeString;
    if (dateTimeString.includes('T')) {
        dateStr = dateTimeString.split('T')[0];
    } else if (dateTimeString.includes(' ')) {
        dateStr = dateTimeString.split(' ')[0];
    }
    return dateStr;
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
                if (data.error) {
                    // Show specific error messages for better user experience
                    if (data.error === 'Complaint number is invalid') {
                        alert('❌ Complaint number is invalid. Please check the number and try again.');
                    } else {
                        alert('Error: ' + data.error);
                    }
                } else {
                    // Populate form fields with API data
                    setValue('job_number', data.JobNo || '');
                    setValue('coms_complaint_date', extractDatePart(data.COMSComplaintDate || data.ComplaintDate || ''));
                    setValue('job_type', data.JobType || '');
                    setValue('customer_name', data.CustomerName || '');
                    setValue('contact_no', data.ContactNo || '');
                    setValue('technician_name', data.TCN_NAME || data.TechnicianName || '');
                    setValue('purchase_date', extractDatePart(data.DateofPurchase || data.PurchaseDate || ''));
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
