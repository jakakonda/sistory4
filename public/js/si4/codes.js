si4.codes = {
    load: function(callback) {
        si4.callMethod({moduleName:'System/Codes', methodName:"getCodes", aSync:true}, function(data) {
            for (var codesSetName in data) {
                si4.codes[codesSetName] = {};
                for (var idx in data[codesSetName]) {
                    var code = data[codesSetName][idx];
                    si4.codes[codesSetName][code["code_id"]] = code["value"];
                }
            }
            if (typeof(callback) == "function") { callback(); }
        });
    }
};


si4.codes.structTypeIds = {
    0:'',
    1:'entity',
    2:'collection'
};

si4.codes.filePurposes = {
    entity: 'entity',
    other: 'other'
};