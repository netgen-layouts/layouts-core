
const formsInit = () => {
    const fileInputs = document.querySelectorAll('input[type=file]');
    [...fileInputs].forEach((i) => {
        i.addEventListener('change', () => {
          const result = i.previousSibling; // catching span to display file name
          result.textContent = `${i.files[0].name}`;
        });
    });
};

export default formsInit;
