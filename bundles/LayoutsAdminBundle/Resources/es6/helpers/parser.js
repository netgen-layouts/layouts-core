const parser = (domstring) => {
    const html = new DOMParser().parseFromString(domstring, 'text/html');
    return Array.from(html.body.childNodes);
};

export default parser;
