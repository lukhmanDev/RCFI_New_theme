<div id="deleteConfirmationModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeDeleteModal()">
    <div class="panel" style="width: 100%; max-width: 420px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
        <button onclick="closeDeleteModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>

        <div class="panel-header" style="margin-bottom: 1rem;">
            <h2 class="panel-title" style="font-size: 1.25rem;">Confirm Delete</h2>
        </div>

        <div style="padding: 0 1.5rem 1rem; color: var(--text-main); line-height: 1.6;">
            <p id="deleteConfirmationText" style="margin: 0; font-weight: 600;">Are you sure you want to delete this partner/donor?</p>
            <p style="margin: 0.75rem 0 0; color: var(--text-muted); font-size: 0.95rem;">This action cannot be undone.</p>
        </div>

        <form id="deleteConfirmationForm" action="" method="POST" style="padding: 0 1.5rem 1.5rem;">
            @csrf
            @method('DELETE')
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" class="btn-custom" onclick="closeDeleteModal()" style="padding: 0.65rem 1.2rem;">Cancel</button>
                <button type="submit" class="btn-danger-custom" style="padding: 0.65rem 1.2rem;">Delete</button>
            </div>
        </form>
    </div>
</div>
