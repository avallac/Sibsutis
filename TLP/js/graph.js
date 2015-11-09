function init() {
    var $ = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
        $(go.Diagram, "myDiagram",  // must name or refer to the DIV HTML element
            {
                // start everything in the middle of the viewport
                initialContentAlignment: go.Spot.Center,
                // have mouse wheel events zoom in and out instead of scroll up and down
                "toolManager.mouseWheelBehavior": go.ToolManager.WheelZoom,
                // support double-click in background creating a new node
                "clickCreatingTool.archetypeNodeData": { text: " " },
                // enable undo & redo
                "undoManager.isEnabled": true
            });

    // when the document is modified, add a "*" to the title and enable the "Save" button
    myDiagram.addDiagramListener("Modified", function(e) {
        var button = document.getElementById("SaveButton");
        if (button) button.disabled = !myDiagram.isModified;
        var idx = document.title.indexOf("*");
        if (myDiagram.isModified) {
            if (idx < 0) document.title += "*";
        } else {
            if (idx >= 0) document.title = document.title.substr(0, idx);
        }
    });

    // define the Node template
    myDiagram.nodeTemplate =
        $(go.Node, "Auto",
            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
            // define the node's outer shape, which will surround the TextBlock
            $(go.Shape, "RoundedRectangle",
                {
                    parameter1: 20,  // the corner has a large radius
                    fill: $(go.Brush, "Linear", { 0: "rgb(254, 201, 0)", 1: "rgb(254, 162, 0)" }),
                    stroke: "black",
                    portId: "",
                    fromLinkable: true,
                    fromLinkableSelfNode: true,
                    fromLinkableDuplicates: true,
                    toLinkable: true,
                    toLinkableSelfNode: true,
                    toLinkableDuplicates: true,
                    cursor: "pointer"
                }),
            $(go.TextBlock,
                {
                    font: "bold 11pt helvetica, bold arial, sans-serif",
                    editable: true  // editing the text automatically updates the model data
                },
                new go.Binding("text", "text").makeTwoWay())
        );

    // replace the default Link template in the linkTemplateMap
    myDiagram.linkTemplate =
        $(go.Link,  // the whole link panel
            { curve: go.Link.Bezier, adjusting: go.Link.Stretch, reshapable: true },
            new go.Binding("curviness", "curviness"),
            new go.Binding("points").makeTwoWay(),
            $(go.Shape,  // the link shape
                { strokeWidth: 1.5 }),
            $(go.Shape,  // the arrowhead
                { toArrow: "standard", stroke: null }),
            $(go.Panel, "Auto",
                $(go.Shape,  // the link shape
                    {
                        fill: $(go.Brush, "Radial",
                            { 0: "rgb(240, 240, 240)", 0.3: "rgb(240, 240, 240)", 1: "rgba(240, 240, 240, 0)" }),
                        stroke: null
                    }),
                $(go.TextBlock, " ",  // the label
                    {
                        textAlign: "center",
                        font: "10pt helvetica, arial, sans-serif",
                        stroke: "black",
                        margin: 4,
                        editable: true  // editing the text automatically updates the model data
                    },
                    new go.Binding("text", "text").makeTwoWay())
            )
        );
}
