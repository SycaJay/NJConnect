<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - Glory Life New Jerusalem Generation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .seat-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 1000px;
            text-align: center;
        }
        .seat-title {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
            background: linear-gradient(to right, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stage {
            width: 80%;
            height: 50px;
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            margin: 0 auto 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .seat-area {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            perspective: 1000px;
        }
        .seat {
            width: 40px;
            height: 40px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            position: relative;
            transform-style: preserve-3d;
        }
        .seat:hover {
            transform: scale(1.05) rotateY(10deg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .seat.selected {
            background-color: #2196F3;
            transform: scale(1.1) rotateY(15deg);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .seat.booked {
            background-color: #f44336;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .seat-legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }
        .legend-color {
            width: 25px;
            height: 25px;
            border-radius: 5px;
        }
        #selected-seats {
            margin: 20px 0;
            font-size: 1.2rem;
            color: #333;
            font-weight: 600;
        }
        .submit-btn {
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .seat-container {
                padding: 20px;
            }
            .seat {
                width: 35px;
                height: 35px;
                font-size: 0.8rem;
            }
        }
        .return-btn {
    background-color: #007BFF; 
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display: block; 
    margin-top: 10px; 
    width: 200px; 
    text-align: center; 
}
    </style>
</head>
<body>
    <div class="seat-container">
        <h1 class="seat-title">Select Your Seat</h1>
        
        <div class="stage">STAGE</div>

        <div class="seat-area" id="seat-grid"></div>
        
        <div class="seat-legend">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #4CAF50;"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #2196F3;"></div>
                <span>Selected</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #f44336;"></div>
                <span>Booked</span>
            </div>
        </div>

        <div id="selected-seats">Select a Seat</div>
        <form id="seat-form" action="../actions/events/select_seat.php" method="POST">
    <input type="hidden" id="selected-seat-number" name="seat_number" value="">
    <!-- Add the event_id from the URL as a hidden input -->
    <input type="hidden" name="event_id" value="<?php echo $_GET['event_id']; ?>"> 
    <button type="submit" class="submit-btn" id="submit-btn" enabled>Confirm Seat</button>
</form>
<a href="Events.php">
    <button type="button" class="return-btn">Return to Events</button>
</a>
    </div>
    

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const seatGrid = document.getElementById('seat-grid');
    const selectedSeatsDisplay = document.getElementById('selected-seats');
    const submitBtn = document.getElementById('submit-btn');
    const seatForm = document.getElementById('seat-form');
    const selectedSeatInput = document.getElementById('selected-seat-number');
    let selectedSeat = null;

    // Fetch booked seats from the database via AJAX
    function fetchBookedSeats() {
        return fetch('../actions/events/booked_seats.php?event_id=' + <?php echo $_GET['event_id']; ?>)
            .then(response => response.json())
            .catch(error => {
                console.error('Error fetching booked seats:', error);
                return [];
            });
    }

    // Render seats with booked seats disabled
    function renderSeats(bookedSeats) {
        const sections = [
            { start: 1, end: 100 },
            { start: 101, end: 200 },
            { start: 201, end: 300 },
            { start: 301, end: 400 },
            { start: 401, end: 500 }
        ];

        sections.forEach(section => {
            const sectionDiv = document.createElement('div');
            sectionDiv.style.display = 'flex';
            sectionDiv.style.flexWrap = 'wrap';
            sectionDiv.style.justifyContent = 'center';
            sectionDiv.style.gap = '10px';
            sectionDiv.style.width = '100%';
            sectionDiv.style.marginBottom = '20px';

            for (let i = section.start; i <= section.end; i++) {
                const seat = document.createElement('button');
                seat.textContent = i;
                seat.classList.add('seat');
                seat.dataset.seatNumber = i;

                // Mark booked seats
                if (bookedSeats.includes(i)) {
                    seat.classList.add('booked');
                    seat.disabled = true;
                }

                seat.addEventListener('click', () => {
                    // Remove selection from previous seat
                    if (selectedSeat) {
                        selectedSeat.classList.remove('selected');
                    }

                    // Select new seat
                    seat.classList.add('selected');
                    selectedSeat = seat;
                    selectedSeatsDisplay.textContent = `Selected Seat: ${i}`;
                    selectedSeatInput.value = i; // Set the hidden input value
                    submitBtn.disabled = false;
                });

                sectionDiv.appendChild(seat);
            }

            seatGrid.appendChild(sectionDiv);
        });
    }

    // Initial setup
    fetchBookedSeats().then(bookedSeats => {
        renderSeats(bookedSeats);
    });

    // Form submission handler
    seatForm.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent default form submission

        console.log('Form submitted');
        console.log('Seat Number:', selectedSeatInput.value);
        console.log('Event ID:', event.target.event_id.value);

        // Create FormData from the form
        const formData = new FormData(seatForm);

        // Send AJAX request to save seat reservation
        fetch('../actions/events/select_seat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            console.log('Server Response:', result);
            
            if (result.includes('Seat reservation confirmed')) {
                alert(result);
                selectedSeat.classList.add('booked');
                selectedSeat.disabled = true;
                submitBtn.disabled = true;
                
                // Optionally, refresh booked seats to ensure latest state
                fetchBookedSeats().then(bookedSeats => {
                    // Clear existing seats and re-render
                    seatGrid.innerHTML = '';
                    renderSeats(bookedSeats);
                });
            } else {
                alert('There was an issue reserving your seat: ' + result);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem reserving your seat.');
        });
    });

    // Initially disable submit button
    submitBtn.disabled = true;
});
    </script>
</body>
</html>