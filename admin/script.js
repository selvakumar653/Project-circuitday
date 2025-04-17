// Add error logging
        window.onerror = function(msg, url, line, col, error) {
            console.error('Error: ' + msg + '\nURL: ' + url + '\nLine: ' + line);
            return false;
        };

        // Improve content loading function
        function loadContent(page) {
            const iframe = document.getElementById('contentFrame');
            const loadingIndicator = document.getElementById('loadingIndicator');
            
            if (!iframe) {
                console.error('Content frame not found');
                return;
            }

            try {
                // Show loading indicator
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'block';
                }

                // Update iframe source
                iframe.src = page;

                // Handle iframe load events
                iframe.onload = function() {
                    if (loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }
                };

                iframe.onerror = function() {
                    console.error('Failed to load page:', page);
                    if (loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }
                    iframe.srcdoc = `
                        <div style="padding: 20px; color: #721c24; background: #f8d7da; border-radius: 4px;">
                            Failed to load content. Please try again.
                        </div>
                    `;
                };
            } catch (error) {
                console.error('Error loading content:', error);
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
            }
        }

        // Function to update active menu item
        function updateActiveMenuItem(page) {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                const link = item.getAttribute('data-page');
                if (link === page) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // Add loading indicator to the page
        document.body.insertAdjacentHTML('beforeend', `
            <div id="loadingIndicator" style="display: none; position: fixed; 
                 top: 50%; left: 50%; transform: translate(-50%, -50%); 
                 background: rgba(255,255,255,0.9); padding: 20px; 
                 border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div class="spinner"></div>
                Loading...
            </div>
        `);

        // Function to toggle settings sub-menu
        function toggleSettings(element) {
            // Toggle active class
            element.classList.toggle('active');
            
            // Find sub-menu
            const subMenu = element.nextElementSibling;
            
            // Toggle sub-menu visibility
            if (subMenu.style.display === 'none') {
                subMenu.style.display = 'block';
                // Animate height
                const height = subMenu.scrollHeight;
                subMenu.style.maxHeight = height + 'px';
            } else {
                subMenu.style.maxHeight = '0';
                setTimeout(() => {
                    subMenu.style.display = 'none';
                }, 300);
            }
            
            // Remove active class from other menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                if (item !== element) {
                    item.classList.remove('active');
                }
            });
        }

        // Replace the updateDashboardCounts function
        function updateDashboardCounts() {
            const userCount = document.getElementById('userCount');
            const orderCount = document.getElementById('orderCount');
            const revenue = document.getElementById('revenue');

            // Show loading state
            userCount.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            orderCount.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            revenue.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('get_counts.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Parse error:', text);
                            throw new Error('Invalid JSON response');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        userCount.textContent = data.counts.users;
                        orderCount.textContent = data.counts.orders;
                        revenue.textContent = '₹' + data.counts.revenue;

                        // Add animation class
                        [userCount, orderCount, revenue].forEach(element => {
                            element.classList.add('count-updated');
                            // Remove animation class after animation completes
                            setTimeout(() => element.classList.remove('count-updated'), 500);
                        });
                    } else {
                        throw new Error(data.error || 'Failed to fetch counts');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = '<i class="fas fa-exclamation-circle"></i> Error';
                    userCount.innerHTML = errorMessage;
                    orderCount.innerHTML = errorMessage;
                    revenue.innerHTML = errorMessage;
                });
        }

        // Update the interval timing and add initial load
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboardCounts();
            // Update every minute
            setInterval(updateDashboardCounts, 60000);
        });

        // Add function to load table data
        function loadTableData() {
            const tableBody = document.getElementById('tableBody');
            
            // Replace this with your actual API call
            fetch('api/get_table_data.php')
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.date}</td>
                            <td>${item.type}</td>
                            <td>${item.description}</td>
                            <td><span class="status-badge ${item.status.toLowerCase()}">${item.status}</span></td>
                            <td>
                                <button onclick="editItem(${item.id})"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteItem(${item.id})"><i class="fas fa-trash"></i></button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error loading table data:', error));
        }

        // Call this function when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadTableData();
        });