const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function () {
    const acceptButtons = document.querySelectorAll('.accept-button');

    acceptButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const itemId = this.getAttribute('data-item-id');
            const parentArticle = this.closest('article');

            if (!csrfToken) {
                console.error("CSRF token is missing!");
                return;
            }

            fetch(`/admin/items/accept/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, 
                },
                body: JSON.stringify({
                    item_id: itemId
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Failed to accept the item.');
                    });
                }
                return response.json();
            })
            .then(() => {
                parentArticle.remove(); 
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            });
        });
    });

    const deleteItemButtons = document.querySelectorAll('.delete-item-button');

    deleteItemButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const itemId = this.getAttribute('data-item-id');
            const parentArticle = this.closest('article');

            if (!csrfToken) {
                console.error("CSRF token is missing!");
                return;
            }

            fetch(`/admin/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, 
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Failed to delete the item.');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log(data.message); 
                parentArticle.remove();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            });
        });
    });

    const deleteUserButtons = document.querySelectorAll('.delete-user-button');

    deleteUserButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const userId = this.getAttribute('data-user-id');
            const parentArticle = this.closest('article');

            if (!csrfToken) {
                console.error("CSRF token is missing!");
                return;
            }

            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, 
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Failed to delete the user.');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log(data.message); 
                parentArticle.remove();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            });
        });
    });

    const banUserButtons = document.querySelectorAll('.ban-user-button');

    banUserButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = this.closest('.ban-user-form');
            const userId = form.getAttribute('data-user-id');
            const banDuration = form.querySelector('select[name="ban_duration"]').value;

            if (!csrfToken) {
                console.error("CSRF token is missing!");
                return;
            }

            fetch(`/admin/users/${userId}/ban`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    ban_duration: banDuration,
                }),
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Failed to ban the user.');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                form.closest('article').classList.add('banned');
                const bannedParagraph = form.closest('article').querySelector('.check-banned');
                if (bannedParagraph) {
                    bannedParagraph.textContent = `Banned: Yes (until ${data.data.ban_until})`;
                }
                form.outerHTML = `<button class="button unban-user-button" data-user-id="${userId}">Unban</button>`;
                attachUnbanHandler();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message); 
            });
        });
    });

    function attachBanHandler() {
        const newBanUserButtons = document.querySelectorAll('.ban-user-button');
        newBanUserButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
            
                const form = this.closest('.ban-user-form');
                if (!form) {
                    console.error('Form not found for ban button.');

                    return;
                }
            
                const article = form?.closest('article');
                if (!article) {
                    console.error('No article found for the given form.');
                    return;
                }
            
                const userId = form.getAttribute('data-user-id');
                const banDuration = form.querySelector('select[name="ban_duration"]').value;
            
                if (!csrfToken) {
                    console.error("CSRF token is missing!");
                    alert('CSRF token is missing!');
                    return;
                }
            
                fetch(`/admin/users/${userId}/ban`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        ban_duration: banDuration,
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Failed to ban the user.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    article.classList.add('banned');
            
                    const bannedParagraph = article.querySelector('.check-banned');
                    if (bannedParagraph) {
                        bannedParagraph.textContent = `Banned: Yes (until ${data.data.ban_until})`;
                    }
            
                    form.outerHTML = `<button class="button unban-user-button" data-user-id="${userId}">Unban</button>`;
                    attachUnbanHandler();
                })
                .catch(error => {
                    console.error('Error:', error);

                });
            });            
        });
    }
    
    function attachUnbanHandler() {
        const unbanUserButtons = document.querySelectorAll('.unban-user-button');
        unbanUserButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
    
                const userId = this.getAttribute('data-user-id');
    
                if (!csrfToken) {
                    console.error("CSRF token is missing!");
                    alert('CSRF token is missing!');
                    return;
                }
    
                fetch(`/admin/users/${userId}/unban`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Failed to unban the user.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); 
    
                    const parentArticle = button.closest('article');
                    if (parentArticle) {
                        const bannedParagraph = parentArticle.querySelector('.check-banned');
                        if (bannedParagraph) {
                            bannedParagraph.textContent = `Banned: No`;
                        }
    
                        button.outerHTML = `
                            <form data-user-id="${userId}" class="ban-user-form">
                                <select class="ban-duration" name="ban_duration" required>
                                    <option value="1 hour">1 Hour</option>
                                    <option value="1 day">1 Day</option>
                                    <option value="1 week">1 Week</option>
                                    <option value="1 month">1 Month</option>
                                </select>
                                <button type="button" class="button ban-user-button">Ban</button>
                            </form>`;
                        attachBanHandler();
                    } else {
                        console.error('No parent article found for unban button.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                });
            });
        });
    }
    
    attachUnbanHandler();
    
    document.body.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('suspend-item-button')) {
            handleSuspend(e.target);
        } else if (e.target && e.target.classList.contains('unsuspend-item-button')) {
            handleUnsuspend(e.target);
        }
    });

    function handleSuspend(button) {
        const itemId = button.getAttribute('data-item-id');

        updateButtonState(button, 'Suspended');

        fetch(`/admin/items/${itemId}/suspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Failed to suspend the item.');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error:', error);

            revertButtonState(button);
        });
    }

    function handleUnsuspend(button) {
        const itemId = button.getAttribute('data-item-id');

        updateButtonState(button, 'Auction');

        fetch(`/admin/items/${itemId}/unsuspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Failed to unsuspend the item.');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
            revertButtonState(button);
        });
    }

    function updateButtonState(button, newState) {
        const parentArticle = button.closest('article');
        if (!parentArticle) {
            console.error('Parent article not found for button:', button);
            return;
        }

        const stateParagraph = parentArticle.querySelector('.item-state');
        if (stateParagraph) {
            stateParagraph.textContent = `State: ${newState}`;
        }

        const newButtonHtml = (newState === 'Suspended') ?
            `<button style = "font-size: 15px !important;" class="button unsuspend-item-button" data-item-id="${button.getAttribute('data-item-id')}">Unsuspend</button>` :
            `<button style = "font-size: 15px !important;" class="button suspend-item-button" data-item-id="${button.getAttribute('data-item-id')}">Suspend</button>`;
        
        button.outerHTML = newButtonHtml;
    }

    function revertButtonState(button) {
        const itemId = button.getAttribute('data-item-id');
        const parentArticle = button.closest('article');
        if (!parentArticle) {
            console.error('Parent article not found for button:', button);
            return;
        }

        const stateParagraph = parentArticle.querySelector('.item-state');
        if (stateParagraph) {
            if (stateParagraph.textContent.includes('Suspended')) {
                stateParagraph.textContent = 'State: Auction';
            } else {
                stateParagraph.textContent = 'State: Suspended';
            }
        }
        const newButtonHtml = (stateParagraph.textContent.includes('Auction')) ?
            `<button style = "font-size: 15px !important;" class="button suspend-item-button" data-item-id="${itemId}">Suspend</button>` :
            `<button style = "font-size: 15px !important;" class="button unsuspend-item-button" data-item-id="${itemId}">Unsuspend</button>`;

        button.outerHTML = newButtonHtml;
    }
});

function addCategory() {
    const value = document.getElementById('newValue').value;
    const type = document.getElementById('categoryContainer').getAttribute('data-type');

    console.log('Type:', type);

    if (!value) {
        alert('Please enter a value.');
        return;
    }

    fetch(`/admin/categories/${type}/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ value })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteReport(reportId) {
    fetch(`/admin/reports/${reportId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (response.ok) {
            document.getElementById(`report-${reportId}`).remove();
            
            const badge = document.querySelector('.badge');
            if (badge) {
                let count = parseInt(badge.textContent);
                if (count > 0) {
                    badge.textContent = count - 1;

                    if (count - 1 === 0) {
                        badge.remove();
                    }
                }
            }
        } else {
            alert('Failed to delete the report.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the report.');
    });
}

function toggleText(reportId) {
    const fullReportText = document.getElementById(`report-text-${reportId}`).getAttribute('data-full-text');
    document.getElementById('modalReportText').innerText = fullReportText;

    const modal = document.getElementById('customModal');
    modal.style.display = 'flex';
}

document.getElementById('closeModal')?.addEventListener('click', function () {
    const modal = document.getElementById('customModal');
    modal.style.display = 'none';
});

window.addEventListener('click', function (event) {
    const modal = document.getElementById('customModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});


function deleteCategory(item) {
    const type = document.getElementById('categoryContainer').dataset.type;
    
    if (confirm(`Are you sure you want to delete the category "${item}"?`)) {
        fetch(`/admin/categories/${type}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ value: item }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("The category is being used.");
            } else {
                location.reload(); 
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
