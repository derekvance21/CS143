// import fs module to read/write file
const fs = require('fs');

// load JSON data
let file = fs.readFileSync("/home/cs143/data/nobel-laureates.json");
let {laureates} = JSON.parse(file)

let buffer = ""

laureates.forEach(laureate => {
    buffer = buffer.concat(JSON.stringify(laureate), "\n") 
});

fs.writeFile("laureates.import", buffer, (err) => err && console.log(err))