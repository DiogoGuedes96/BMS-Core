<?php
if (env('BMS_CLIENT') == 'UNI') {
  return array(
    'Users' => true,
    'Dashboard' => true,
    'UniClients' => true,
    'Notification' => true,
    'Products' => true,
    'Business' => true,
    'UniDashboard' => true,
    'ActiveCampaign' => true,
  );
}

if (env('BMS_CLIENT') == 'ATRAVEL') {
  return array(
    'Users' => true,
    'Clients' => false,
    'Products' => false,
    'Primavera' => false,
    'Orders' => false,
    'Calls' => false,
    'Dashboard' => true,
    'Schedule' => false,
    'Patients' => false,
    'Routes' => true,
    'Tables' => true,
    'ServiceScheduling' => false,
    'Feedback' => false,
    'Workers' => true,
    'Bookings' => true,
    'Vehicles' => true,
    'Services' => true,
    'AmbulanceCrew' => true,
    'Companies' => true,
  );
}

if (env('BMS_CLIENT') == 'ASM') {
  return array(
    'Users' => true,
    'Clients' => true,
    'Products' => false,
    'Primavera' => false,
    'Orders' => false,
    'Calls' => true,
    'Dashboard' => true,
    'Schedule' => false,
    'Patients' => true,
    'Routes' => true,
    'Tables' => true,
    'ServiceScheduling' => true,
    'Feedback' => true,
    'Workers' => true,
    'Bookings' => true,
    'Vehicles' => true,
    'Services' => true,
    'AmbulanceCrew' => true,
  );
}
