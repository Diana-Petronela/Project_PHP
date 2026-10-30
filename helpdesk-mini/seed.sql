-- Optional demo data. Login with: demo@helpdesk.local / password123

USE helpdesk_mini;

INSERT INTO users (id, name, email, password_hash) VALUES
    (1, 'Demo User', 'demo@helpdesk.local', '$2y$10$dhNNJqXbAU.H0nMB1SntFOICwC1fj82U5kHDIjWZgj40Ytwt80Ei2');

INSERT INTO tickets (id, user_id, title, description, status) VALUES
    (1, 1, 'Laptop will not turn on',        'Pressed the power button several times, no response at all.', 'Open'),
    (2, 1, 'Cannot connect to office Wi-Fi', 'Laptop sees the network but the password is rejected.',       'In Progress'),
    (3, 1, 'Waiting for replacement mouse',  'Ordered a new mouse, ticket is paused until it arrives.',     'On Hold'),
    (4, 1, 'Printer not detected',           'The office printer stopped showing up on the network.',       'Closed');

INSERT INTO ticket_notes (ticket_id, note) VALUES
    (2, 'Restarted the router, still no connection.'),
    (2, 'Confirmed the Wi-Fi password is correct - forgot the network and reconnected, same error.'),
    (4, 'Reinstalled the printer driver, it shows up again now.');
