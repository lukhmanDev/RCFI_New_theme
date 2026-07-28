
        (function() {
            const csrfToken = "1";

            function findTargetPhotoCard(category) {
                if (!category) return null;
                const catLower = category.toLowerCase().trim();

                const cardByAttr = document.querySelector(`.photo-card[data-category="${category}"]`);
                if (cardByAttr) return cardByAttr;

                const categoryTitleMap = {
                    'before': 'before',
                    'starting': 'starting',
                    'inbetween': 'in between',
                    'after': 'final',
                    'banner': 'banner',
                    'stone': 'stone',
                    'inauguration': 'inauguration'
                };

                const searchKey = categoryTitleMap[catLower] || catLower;
                const cards = document.querySelectorAll('.photo-card');
                for (let c of cards) {
                    const titleText = c.querySelector('.photo-card-title')?.textContent?.toLowerCase() || '';
                    if (titleText.includes(searchKey) || c.getAttribute('data-category') === category) {
                        return c;
                    }
                }

                const categoryMap = {
                    'before': 0, 'starting': 1, 'inbetween': 2, 'after': 3, 'banner': 4, 'stone': 5, 'inauguration': 6
                };
                const idx = categoryMap[catLower];
                if (idx !== undefined && cards[idx]) return cards[idx];

                return null;
            }

            function renderPhotoInDOM(data) {
                const category = data.category || 'after';
                const photoUrl = data.photo_url || data.path;
                const deleteUrl = data.delete_url || (`1/${data.photo_index ?? data.index}?category=${category}`);
                const totalPhotos = data.total_photos !== undefined ? data.total_photos : ((data.photo_index ?? data.index) + 1);

                const targetCard = findTargetPhotoCard(category);

                if (targetCard) {
                    const badge = targetCard.querySelector('.photo-card-header span:last-child');
                    if (badge) badge.textContent = totalPhotos;

                    const container = targetCard.querySelector('.photo-list-container');
                    if (container) {
                        const emptyState = container.querySelector('.photo-empty-state');
                        if (emptyState) emptyState.remove();

                        const photoDiv = document.createElement('div');
                        photoDiv.style.cssText = 'position: relative; background: var(--bg-color); border: 1px solid var(--panel-border); border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: all 0.3s ease;';
                        
                        photoDiv.innerHTML = `
                            <a href="${photoUrl}" target="_blank" style="display: block; line-height: 0;">
                                <img src="${photoUrl}" style="width: 100%; height: 120px; object-fit: cover; display: block;" alt="Photo ${totalPhotos}">
                            </a>
                            <form action="${deleteUrl}" method="POST" style="position: absolute; top: 0.3rem; right: 0.3rem; margin: 0;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(231,76,60,0.9); border: none; color: #fff; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.5);" title="Delete Photo">
                                    <i class="bx bx-trash" style="font-size: 0.8rem;"></i>
                                </button>
                            </form>
                            <div style="padding: 0.3rem 0.5rem; font-size: 0.72rem; color: var(--text-muted);">
                                Photo ${totalPhotos}
                            </div>
                        `;
                        container.appendChild(photoDiv);
                    }
                }
            }

            if (window.__photoSubmitHandler) {
                document.removeEventListener('submit', window.__photoSubmitHandler, true);
            }

            window.__photoSubmitHandler = async function(e) {
                const form = e.target;
                if (!form || form.getAttribute('data-no-ajax') === 'true') return;

                const action = form.action || '';

                // A. AJAX PHOTO UPLOAD (Matches both upload-photo and upload_photo)
                if (action.includes('upload-photo') || action.includes('upload_photo')) {
                    e.preventDefault();
                    if (form.dataset.submitting === 'true') return;
                    form.dataset.submitting = 'true';

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const origBtnText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Uploading...';
                    }

                    try {
                        const response = await fetch(action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            renderPhotoInDOM(data);
                            const fileInput = form.querySelector('input[type="file"]');
                            if (fileInput) fileInput.value = '';

                            if (typeof showToast === 'function') {
                                showToast(data.message || 'Photo uploaded successfully!', 'success');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast(data.message || 'Photo upload failed.', 'danger');
                            } else {
                                alert(data.message || 'Photo upload failed.');
                            }
                        }
                    } catch (err) {
                        console.error('AJAX upload photo error:', err);
                        alert('Photo upload failed. Please try again.');
                    } finally {
                        delete form.dataset.submitting;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origBtnText;
                        }
                    }
                }

                // B. AJAX PHOTO DELETE (Matches delete-photo, delete_photo, /photos/)
                else if (action.includes('delete-photo') || action.includes('delete_photo') || action.includes('/photos/')) {
                    e.preventDefault();
                    if (form.dataset.submitting === 'true') return;

                    const doDelete = async () => {
                        form.dataset.submitting = 'true';
                        const photoItem = form.closest('div[style*="position: relative"]');
                        const card = form.closest('.photo-card');

                        try {
                            const response = await fetch(action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: new FormData(form)
                            });

                            const data = await response.json();

                            if (data.success) {
                                if (photoItem) {
                                    photoItem.style.transition = 'all 0.3s ease';
                                    photoItem.style.opacity = '0';
                                    photoItem.style.transform = 'scale(0.8)';
                                    setTimeout(() => {
                                        photoItem.remove();
                                        if (card) {
                                            const badge = card.querySelector('.photo-card-header span:last-child');
                                            if (badge && data.total_photos !== undefined) badge.textContent = data.total_photos;

                                            const container = card.querySelector('.photo-list-container');
                                            if (container && (data.total_photos === 0 || container.children.length === 0)) {
                                                const cardTitle = card.querySelector('.photo-card-title')?.textContent?.toLowerCase() || '';
                                                container.innerHTML = `<div class="photo-empty-state">No ${cardTitle} photos yet.</div>`;
                                            }
                                        }
                                    }, 300);
                                }
                                if (typeof showToast === 'function') {
                                    showToast(data.message || 'Photo deleted successfully!', 'success');
                                }
                            } else {
                                if (typeof showToast === 'function') {
                                    showToast(data.message || 'Photo delete failed.', 'danger');
                                } else {
                                    alert(data.message || 'Photo delete failed.');
                                }
                            }
                        } catch (err) {
                            console.error('AJAX delete photo error:', err);
                            alert('Photo delete failed. Please try again.');
                        } finally {
                            delete form.dataset.submitting;
                        }
                    };

                    if (typeof showCustomConfirm === 'function') {
                        showCustomConfirm('Delete this photo?', doDelete);
                    } else if (confirm('Delete this photo?')) {
                        doDelete();
                    }
                }
            };

            document.addEventListener('submit', window.__photoSubmitHandler, true);
        })();
    