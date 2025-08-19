export function getSubObjectValue(data, column) {
    const columns = column.split("__");
    if(columns.length <= 1) return data[column]
    var result = data;
    columns.forEach((item, index) => {
       result = result[item]??''
    });
    return result;
}