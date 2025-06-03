// progress-bar animation in a about me section
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const bars = entry.target.querySelectorAll('.progress-bar div');
            bars.forEach((bar, i) => {
                // Re-trigger animation by resetting style
                bar.style.animation = 'none';
                bar.offsetHeight; // trigger reflow
                bar.style.animation = `bar-anim 1s ease-in-out forwards, pulse 0.6s ease 1s 1`;
                bar.style.animationDelay = `${i * 0.05}s, ${i * 0.05 + 1}s`;
            });

            // Stop observing after animation triggered once
            // observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

// Observe the container that holds all skills
const skillSet = document.querySelector('.skill-set');
if (skillSet) observer.observe(skillSet);



// Set title attribute for the skill set container (tooltip)
document.querySelectorAll('.progress-bar > div').forEach(bar => {
    const width = bar.style.width;
    bar.setAttribute('title', width);
});

// Add labels to progress bars
document.querySelectorAll('.progress-bar > div').forEach(bar => {
    const width = bar.style.width;
    const label = document.createElement('span');
    label.textContent = width;
    label.style.position = 'absolute';
    label.style.right = '5px';
    label.style.top = '50%';
    label.style.transform = 'translateY(-50%)';
    label.style.color = '#fff';
    label.style.fontSize = '0.8rem';
    bar.style.position = 'relative';  // Make sure parent is positioned
    bar.appendChild(label);
});



// Collapsible sections for skills
document.addEventListener('DOMContentLoaded', () => {
    const collapsibles = document.querySelectorAll('.collapsible');
    collapsibles.forEach(btn => {
        btn.addEventListener('click', function () {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            content.classList.toggle('active');
            btn.style.borderRadius = content.classList.contains('active') ? '6px 6px 0 0' : '6px';
        });
    });
});

// observe timline-content and animate timeline items
const timelineObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('grow-in');
        }
    });
}, { threshold: 0.4 });

// Observe the container that holds all skills
const timeline = document.querySelector('.experience-timeline');
if (timeline) timelineObserver.observe(timeline);



// soft skills wheel
function createRadarChart() {
    const ctx = document
        .getElementById("softSkillsChart")
        .getContext("2d");

    radarChart = new Chart(ctx, {
        type: "radar",
        data: {
            // axes of the radar (skills)
            labels: [
                "Communication",
                "Problem-Solving",
                "Adaptability",
                "Teamwork",
                "Time Management",
                "Initiative",
                "Attention to Detail",
                "Creativity",
            ],
            datasets: [
                {
                    label: "My Soft Skills",
                    data: [8, 9, 9, 8, 7, 6, 8, 7], // scores for each skill
                    backgroundColor: "rgba(255, 161, 84, 0.2)",
                    borderColor: "rgba(255, 183, 124, 1)", // line color
                    borderWidth: 2,
                    pointBackgroundColor: "rgb(255, 169, 99)", // dot color
                    pointBorderColor: "rgb(255, 144, 53)", // dot border color
                },
            ],
        },
        options: {
            animation: {
                duration: 1500,
                easing: 'easeOutBounce'
            },
            responsive: true,
            scales: {
                r: {
                    angleLines: { display: true },
                    suggestedMin: 0,
                    suggestedMax: 10,
                    ticks: {
                        stepSize: 2,
                        backdropColor: "transparent",
                        color: "rgb(85, 28, 13)",
                    },
                    pointLabels: {
                        font: { size: 14 },
                        color: "rgb(85, 28, 13)",
                    },
                },
            },
            plugins: {
                legend: {
                    display: false, // hide legend
                },
            },
        },
    });

}

// Animation observer for the radar chart
let radarChart;

const radarSection = document.querySelector('.soft-skills-section');

const radarObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            if (radarChart)
                radarChart.destroy();
            createRadarChart();

        }
    });
}, {
    threshold: 0.5
});

radarObserver.observe(radarSection);



// Validation for contact form
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".contact-form");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        // console.log("Form submitted!");
        // Clear old errors
        const errorElements = form.querySelectorAll(".error");
        errorElements.forEach((el) => el.remove());

        let hasError = false;

        // Validate Name
        const name = form.elements["name"];
        if (!name.value.trim() || name.value.trim().length < 2) {
            showError(name, "Please enter a valid name (at least 2 characters).");
            hasError = true;
        }

        // Validate Email
        const email = form.elements["email"];
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim() || !emailRegex.test(email.value.trim())) {
            showError(email, "Please enter a valid email address.");
            hasError = true;
        }

        // Validate Message
        const message = form.elements["message"];
        if (!message.value.trim() || message.value.trim().length < 10) {
            showError(message, "Your message must be at least 10 characters long.");
            hasError = true;
        }

        if (hasError) {
            e.preventDefault(); // stop form submission
        }
    });
});

function showError(inputElement, message) {
    const error = document.createElement("div");
    error.className = "error";
    error.textContent = message;
    inputElement.insertAdjacentElement("afterend", error);
}