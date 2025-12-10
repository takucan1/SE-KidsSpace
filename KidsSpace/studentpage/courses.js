function toggleAccordion(element) {
    const content = element.nextElementSibling;
    const icon = element.querySelector('i');
    const isOpen = content.classList.contains('open');

    // Close all other accordions
    document.querySelectorAll('.accordion-content').forEach(c => c.classList.remove('open'));
    document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));

    if (!isOpen) {
        content.classList.add('open');
        element.classList.add('active');
    }
}