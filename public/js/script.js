// Track if event listener is already attached
if (!window.complaintSearchListenerAttached) {
    console.log('Attaching complaint search listener');

    const btn = document.getElementById('searchComplaintBtn');
    if (btn) {
        // Helper to safely set value if element exists
        const setValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.value = value || '';
        };

        btn.addEventListener('click', function () {
            const complaintInput = document.getElementById('complaint_number');
            const complaintNo = complaintInput ? complaintInput.value : '';

            console.log('Complaint search button clicked, complaintNo:', complaintNo);

            if (complaintNo && complaintNo.trim() !== "") {
                console.log('Searching for complaint:', complaintNo);

                fetch(`/api/get-complaint/${encodeURIComponent(complaintNo)}`) // <-- Your Laravel API endpoint
                    .then(response => response.json())
                    .then(data => {
                        console.log('Received complaint data:', data);

                        if (data) {
                            setValue('job_number', data.job_number);
                            setValue('coms_complaint_date', data.coms_complaint_date);
                            setValue('job_type', data.job_type);
                            setValue('customer_name', data.customer_name);
                            setValue('contact_no', data.contact_no);
                            setValue('technician_name', data.technician_name);
                            setValue('purchase_date', data.purchase_date);
                            setValue('product', data.product);
                            setValue('job_status', data.job_status);
                            setValue('problem', data.problem);
                            setValue('workdone', data.workdone);
                        } else {
                            alert("No record found for this Complaint #");
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
            } else {
                alert("Please enter a Complaint #");
            }
        });

        window.complaintSearchListenerAttached = true;
    }
}