document.addEventListener('DOMContentLoaded', () => {
    // =============================
    // COMMENT FORM HANDLING
    // =============================
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('post_comment', 'true');
            const csrfToken = form.querySelector('[name="csrf_token"]').value;

            form.classList.add('loading');

            try {
                const response = await fetch('like_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(formData)
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment';
                    commentDiv.dataset.commentId = data.comment.id;
                    commentDiv.innerHTML = `
                        <div class="comment-header" style="display: flex; justify-content: space-between;">
                            <div>
                                <strong>${escapeHTML(data.comment.username)}</strong>
                                <span>${escapeHTML(data.comment.date)}</span>
                            </div>
                            <button class="delete-comment-btn btn" style="background-color:#dc3545; font-size:0.8rem; padding:4px 8px;">Delete</button>
                        </div>
                        <p>${data.comment.text}</p>
                    `;

                    const list = form.closest('.comments-section').querySelector('.comments-list');
                    list.prepend(commentDiv);
                    form.querySelector('textarea').value = '';

                    Swal.fire({
                        icon: 'success',
                        title: 'Comment posted!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    attachDeleteHandler(commentDiv.querySelector('.delete-comment-btn')); // Bind delete event
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Something went wrong.'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Failed to post comment.'
                });
            } finally {
                form.classList.remove('loading');
            }
        });
    });

    // =============================
    // DELETE COMMENT HANDLING
    // =============================
    function attachDeleteHandler(button) {
        button.addEventListener('click', async () => {
            const commentDiv = button.closest('.comment');
            const commentId = commentDiv.dataset.commentId;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            if (!confirm("Are you sure you want to delete this comment?")) return;

            try {
                const response = await fetch('like_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        csrf_token: csrfToken,
                        recipe_id: document.querySelector('[data-recipe-id]')?.dataset.recipeId || 0,
                        delete_comment: true,
                        comment_id: commentId
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    commentDiv.remove();
                } else {
                    alert(data.message || 'Could not delete comment.');
                }
            } catch (err) {
                alert('Network error. Please try again.');
            }
        });
    }

    document.querySelectorAll('.delete-comment-btn').forEach(btn => attachDeleteHandler(btn));

    // =============================
    // LIKE BUTTON HANDLING
    // =============================
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const recipeId = button.dataset.recipeId;
            const isLiked = button.classList.contains('liked');
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            try {
                const response = await fetch('like_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        csrf_token: csrfToken,
                        recipe_id: recipeId,
                        action: isLiked ? 'unlike' : 'like'
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    button.classList.toggle('liked');
                    button.innerHTML = `❤️ ${data.likes}`;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Like action failed.'
                    });
                }
            } catch (err) {
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Failed to update like.'
                });
            }
        });
    });

    // =============================
    // ESCAPE HTML UTILITY
    // =============================
    function escapeHTML(text) {
        return text.replace(/[&<>"']/g, match => {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            };
            return map[match];
        });
    }

    // =============================
    // SCROLL TO TOP BUTTON
    // =============================
    const scrollBtn = document.getElementById('scrollToTop');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });

        scrollBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
