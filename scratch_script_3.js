
                        (function() {
                            function recalcSubFinancialTotal() {
                                var totalAmount = parseFloat(document.getElementById('fin_total_amount')?.value) || 0;
                                var comm = parseFloat(document.getElementById('fin_community_contribution')?.value) || 0;
                                var donor = parseFloat(document.getElementById('fin_amount_paid_by_donor')?.value) || 0;
                                var anyOther = parseFloat(document.getElementById('fin_any_other')?.value) || 0;
                                var deductions = parseFloat(document.getElementById('fin_deductions')?.value) || 0;

                                var total = totalAmount + comm + donor + anyOther - deductions;
                                if (total < 0) total = 0;

                                var elCost = document.getElementById('fin_total_project_cost');
                                if (elCost) {
                                    elCost.value = total.toFixed(2);
                                }
                            }

                            ['fin_total_amount', 'fin_community_contribution', 'fin_amount_paid_by_donor', 'fin_any_other', 'fin_deductions'].forEach(function(id) {
                                var el = document.getElementById(id);
                                if (el) {
                                    el.addEventListener('input', recalcSubFinancialTotal);
                                    el.addEventListener('change', recalcSubFinancialTotal);
                                }
                            });

                            recalcSubFinancialTotal();
                        })();
                        