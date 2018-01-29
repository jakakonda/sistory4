var F = function(args){

    args.createMainTab();
    args.createContentTab();

    var name = "file";
    var dataTable = new si4.widget.si4DataTable({
        parent: args.contentTab.content.selector,
        primaryKey: ['id'],
        entityTitleNew: si4.lookup[name].entityTitleNew,
        entityTitleEdit: si4.lookup[name].entityTitleEdit,
        //filter: { enabled: false },
        dataSource: new si4.widget.si4DataTableDataSource({
            select: si4.api["fileList"],
            delete: si4.api["deleteFile"],
            staticData : { },
            pageCount: 20
        }),
        editorModuleArgs: {
            moduleName:"Files/FileDetails",
        },
        canInsert: true,
        canDelete: true,
        tabPage: args.contentTab,
        fields: {
            //id: { caption: "Id" },
            url: { visible: false },
        },
        cssClass_table: "si4DataTable_table width100percent"
    });

    /*
    var importForm = new si4.widget.si4Form({parent:si4.data.contentElement });
    var importFile = importForm.addInput({name:"file", value:"", type:"file", accept: ".zip" });
    importFile.displayNone();
    importFile.selector.change(function() {
        console.log("change", importFile.getValue());

        var url = "/admin/upload/import";
        var formData = new FormData();
        formData.append("file", importFile.getValue());
        console.log("post ", url, formData);

        if (confirm(si4.translate("text_confirm_import_entities"))) {

            si4.loading.show();

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    console.log("callback", response);
                    setTimeout(function() {
                        dataTable.refresh();
                        si4.loading.hide();
                    }, 2000);
                }
            });
        }

    });

    args.entityImportButton = args.createContentTab("importTab", { type: "button" });
    args.entityImportButton.onActive(function(e) {
        console.log("import", e);
        importFile.input.selector.click();
    });


    args.entityExportButton = args.createContentTab("exportTab", { type: "button" });
    args.entityExportButton.onActive(function() {
        var url = "/admin/download/export";
        var postData = dataTable.dataSource.getMethodCallData(dataTable.dataSource.methodNames.select);

        var exportForm = document.createElement("form");
        exportForm.action = url;
        exportForm.method = "POST";

        var dataInput = document.createElement("input");
        dataInput.name = "data";
        dataInput.type = "hidden";
        dataInput.value = JSON.stringify(postData);
        exportForm.appendChild(dataInput);

        si4.data.contentElement.append(exportForm);

        exportForm.submit();
    });
    */
};