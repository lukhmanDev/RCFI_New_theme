
                        function toggleAddressEdit() {
                            const display = document.getElementById('address-display-view');
                            const form = document.getElementById('address-edit-form');
                            const btn = document.getElementById('edit-address-btn');
                            if (form.style.display === 'none') {
                                form.style.display = 'block';
                                display.style.display = 'none';
                                btn.style.display = 'none';
                            } else {
                                form.style.display = 'none';
                                display.style.display = 'block';
                                btn.style.display = 'inline-flex';
                            }
                        }
                    