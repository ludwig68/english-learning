// assets/js/app.js

document.addEventListener('DOMContentLoaded', () => {
    // Flip flashcards
    document.querySelectorAll('.flashcard').forEach(card => {
        card.addEventListener('click', () => {
            card.classList.toggle('is-flipped');
        });
    });

    // Practice AJAX check
    const practiceForm = document.getElementById('practice-form');
    if (practiceForm) {
        practiceForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(practiceForm);

            try {
                const res = await fetch('/ajax/check_answer.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                Swal.fire({
                    title: data.correct ? 'Chính xác!' : 'Chưa đúng rồi',
                    html: data.message || '',
                    icon: data.correct ? 'success' : 'error',
                    showClass: {
                        popup: data.correct
                            ? 'animate__animated animate__fadeInDown'
                            : 'animate__animated animate__headShake'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                }).then(() => {
                    window.location.reload();
                });
            } catch (err) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra, thử lại sau.',
                    icon: 'error'
                });
            }
        });
    }
});
