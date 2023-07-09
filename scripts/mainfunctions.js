document.getElementById("nojs").style.display = "none";

String.prototype.replaceAt = function (index, replacement) {
    return this.substring(0, index) + replacement + this.substring(index, this.length).substring(1); //replacement.length
}

String.prototype.biReplaceAt = function (index1, replacement1, index2, replacement2) {
    if (index1 < index2)
        return this.substring(0, index1) + replacement1 + this.substring(index1, index2).substring(1) + replacement2 + this.substring(index2, this.length).substring(1); //replacement.length
    else
        return this.substring(0, index2) + replacement2 + this.substring(index2, index1).substring(1) + replacement1 + this.substring(index1, this.length).substring(1); //replacement.length
}

function deleteMsg(type) {
    const obj = document.getElementById(`${type}Msg`);
    if (obj) {
        obj.style.transform = "scale(0) rotate(45deg)";
        obj.style.margin = "0px";
        // obj.style.display = 'none';
    }
    else return 1
}

async function copytcb(text) {
    try {
        await navigator.clipboard.writeText(text);
        console.log('Content copied to clipboard');
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}