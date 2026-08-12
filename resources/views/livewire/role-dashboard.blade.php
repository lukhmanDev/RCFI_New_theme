<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Welcome Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div>
            <h1 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0;">Welcome, {{ $user->name }}!</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">
                Role assigned: <strong style="color: #10b981;">{{ $user->role_name }}</strong>
            </p>
        </div>
    </div>

    <!-- Real-Time Metrics & Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1.25rem;">
        <!-- Total Applications -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-file"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Applications</span>
                @if($newApplicationsCount > 0)
                    <span style="background: #ef4444; color: #ffffff; padding: 0.15rem 0.55rem; border-radius: 12px; font-size: 0.72rem; font-weight: 800; margin-left: 0.35rem; display: inline-block;">+{{ $newApplicationsCount }} New</span>
                @endif
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $totalApplications }}</h2>
                <span style="color: #3b82f6; font-size: 0.75rem; font-weight: 600;">Total Submissions</span>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #fff7ed; color: #f97316; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-time-five"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Pending Review</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $pendingCount }}</h2>
                <span style="color: #f97316; font-size: 0.75rem; font-weight: 600;">Awaiting Action</span>
            </div>
        </div>

        <!-- Approved Applications -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #ecfdf5; color: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-check-circle"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Approved</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $approvedCount }}</h2>
                <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">Verified Success</span>
            </div>
        </div>

        <!-- Projects Metrics -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #f3e8ff; color: #8b5cf6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-folder"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Running Projects</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $runningProjects }}</h2>
                <span style="color: #8b5cf6; font-size: 0.75rem; font-weight: 600;">Active Pipeline</span>
            </div>
        </div>
    </div>

    <!-- Beneficiary Stats Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">

        <!-- Total Benefited Peoples -->
        <div style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(14, 165, 233, 0.22); display: flex; align-items: center; gap: 1.1rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -16px; top: -16px; width: 90px; height: 90px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div style="position: absolute; right: 20px; bottom: -24px; width: 60px; height: 60px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
            <div style="background: rgba(255,255,255,0.18); color: #ffffff; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0; z-index: 1;">
                <i class="bx bxs-group"></i>
            </div>
            <div style="z-index: 1;">
                <span style="color: rgba(255,255,255,0.85); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block;">Total Benefited Peoples</span>
                <h2 style="color: #ffffff; font-size: 2rem; font-weight: 900; margin: 0.15rem 0; line-height: 1;">{{ number_format($totalBeneficiaryPeoples) }}</h2>
                <span style="color: rgba(255,255,255,0.7); font-size: 0.75rem; font-weight: 600;">Across all projects</span>
            </div>
        </div>

        <!-- Total Benefited Families -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.22); display: flex; align-items: center; gap: 1.1rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -16px; top: -16px; width: 90px; height: 90px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div style="position: absolute; right: 20px; bottom: -24px; width: 60px; height: 60px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
            <div style="background: rgba(255,255,255,0.18); color: #ffffff; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0; z-index: 1;">
                <i class="bx bxs-home-heart"></i>
            </div>
            <div style="z-index: 1;">
                <span style="color: rgba(255,255,255,0.85); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block;">Total Benefited Families</span>
                <h2 style="color: #ffffff; font-size: 2rem; font-weight: 900; margin: 0.15rem 0; line-height: 1;">{{ number_format($totalBeneficiaryFamily) }}</h2>
                <span style="color: rgba(255,255,255,0.7); font-size: 0.75rem; font-weight: 600;">Across all projects</span>
            </div>
        </div>

        <!-- Completed Projects -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(245, 158, 11, 0.22); display: flex; align-items: center; gap: 1.1rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -16px; top: -16px; width: 90px; height: 90px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div style="position: absolute; right: 20px; bottom: -24px; width: 60px; height: 60px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
            <div style="background: rgba(255,255,255,0.18); color: #ffffff; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0; z-index: 1;">
                <i class="bx bxs-badge-check"></i>
            </div>
            <div style="z-index: 1;">
                <span style="color: rgba(255,255,255,0.85); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block;">Completed Projects</span>
                <h2 style="color: #ffffff; font-size: 2rem; font-weight: 900; margin: 0.15rem 0; line-height: 1;">{{ number_format($completedProjects) }}</h2>
                <span style="color: rgba(255,255,255,0.7); font-size: 0.75rem; font-weight: 600;">Successfully delivered</span>
            </div>
        </div>
    </div>

    <!-- Year-wise Beneficiary Chart -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="color: #0f172a; font-size: 1.05rem; font-weight: 800; margin: 0;">Year-wise Impact Overview</h3>
                <p style="color: #64748b; font-size: 0.82rem; margin: 0.2rem 0 0;">Total benefited peoples & families per year</p>
            </div>
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <span style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: #0ea5e9;">
                    <span style="width: 12px; height: 12px; background: #0ea5e9; border-radius: 3px; display: inline-block;"></span>
                    Peoples
                </span>
                <span style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: #10b981;">
                    <span style="width: 12px; height: 12px; background: #10b981; border-radius: 3px; display: inline-block;"></span>
                    Families
                </span>
            </div>
        </div>
        <div style="position: relative; height: 280px;">
            <canvas id="beneficiaryYearChart" style="width: 100%; height: 100%;"></canvas>
        </div>
    </div>

    <!-- Request Leave Modal Dialog Component -->
    @include('partials.leave_request_modal')

    <!-- Chart.js Beneficiary Year Chart -->
    <script>
    (function() {
        var labels   = @json($beneficiaryChartData['labels']);
        var peoples  = @json($beneficiaryChartData['peoples']);
        var families = @json($beneficiaryChartData['families']);

        function initBeneficiaryChart() {
            var ctx = document.getElementById('beneficiaryYearChart');
            if (!ctx) return;
            if (window.beneficiaryYearChartInstance) {
                window.beneficiaryYearChartInstance.destroy();
            }
            window.beneficiaryYearChartInstance = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Benefited Peoples',
                            data: peoples,
                            backgroundColor: 'rgba(14, 165, 233, 0.82)',
                            borderColor: '#0ea5e9',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Benefited Families',
                            data: families,
                            backgroundColor: 'rgba(16, 185, 129, 0.82)',
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            bodyColor: '#f8fafc',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 12, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11 },
                                callback: function(val) {
                                    if (val >= 1000) return (val / 1000).toFixed(1) + 'k';
                                    return val;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Wait for Chart.js to be available
        if (typeof Chart !== 'undefined') {
            initBeneficiaryChart();
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                // Retry a few times in case Chart.js loads async
                var attempts = 0;
                var interval = setInterval(function() {
                    if (typeof Chart !== 'undefined') {
                        clearInterval(interval);
                        initBeneficiaryChart();
                    } else if (++attempts > 20) {
                        clearInterval(interval);
                    }
                }, 200);
            });
        }

        // Livewire re-renders
        document.addEventListener('livewire:navigated', initBeneficiaryChart);
        document.addEventListener('livewire:update', function() {
            setTimeout(initBeneficiaryChart, 100);
        });
    })();
    </script>
</div>
