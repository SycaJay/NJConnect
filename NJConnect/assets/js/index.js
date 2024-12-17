document.addEventListener('DOMContentLoaded', () => {
    const flyerContainer = document.querySelector('.flyer-container');

    // Function to toggle flyer visibility
    const toggleFlyer = () => {
        flyerContainer.style.display = 'block';
        
        // Automatically hide the flyer after 6 seconds to match the animation duration
        setTimeout(() => {
            flyerContainer.style.display = 'none';
        }, 6000); // 6 seconds
    };

    // Start the animation loop
    setInterval(() => {
        toggleFlyer();
    }, 8000); // 8 seconds interval for smooth looping
});
