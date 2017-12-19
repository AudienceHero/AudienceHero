const importer = [];

export function addImporter(component) {
    importer.push(component);
}

export function getImporters() {
    return importer;
}
