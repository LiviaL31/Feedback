document.addEventListener('DOMContentLoaded', () => {
    fetch('vizualizare_feedback.php')
        .then(response => response.json())
        .then(data => {
            const feedbackList = document.getElementById('feedback-list');
            data.forEach(item => {
                const formTitle = document.createElement('h3');
                formTitle.textContent = item.form.title;
                feedbackList.appendChild(formTitle);
                
                item.feedbacks.forEach(feedback => {
                    const feedbackItem = document.createElement('p');
                    feedbackItem.textContent = `Emoție: ${feedback.emotion}, Răspunsuri: ${JSON.stringify(feedback.responses)}`;
                    feedbackList.appendChild(feedbackItem);
                });
            });
        });
});