 const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        item.addEventListener('click', () => {
            // Toggle the 'open' class
            item.classList.toggle('open');

            // Show or hide the answer
            const answer = item.querySelector('p');
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
            } else {
                answer.style.display = 'block';
            }
        });
    });
