function Calc()
{
    var calc = this;
    this.textFiled = $('#calcText');
    this.bind = function() {
        for(var i=0; i<10; i++){
            $("#b"+i).bind( "click", this.bindN(i));
        }
        $("#dot").bind("click", function(){ calc.append('.')});
        $("#ba").bind("click", function(){ calc.action('a')});
        $("#bs").bind("click", function(){ calc.action('s')});
        $("#bd").bind("click", function(){ calc.action('d')});
        $("#bm").bind("click", function(){ calc.action('m')});
        $("#breset").bind("click", function(){ calc.restart()});
        $("#binvert").bind("click", function(){ calc.invert()});
        $("#bc").bind("click", function(){ calc.result()});
        $("#bcorect").bind("click", function(){ calc.correct()});
    };
    this.correct = function(){
        if(this.textFiled.text().length > 1) {
            this.textFiled.text(this.textFiled.text().substring(0, this.textFiled.text().length - 1));
        }else{
            this.textFiled.text(0);
        }
    };
    this.bindN = function(i){
        return function() { calc.append(i); };
    };
    this.restart = function() {
        this.usOldButton();
        this.mode = 0;
        this.savedNum = 0;
        this.savedNum2 = 0;
        this.savedOper = '';
        $('#calcText').text('0');
    };
    this.usOldButton = function() {
        if (this.savedOper != '') {
            var button = $('#b' + this.savedOper);
            button.addClass('color-lightblue');
            button.removeClass('color-black');
        }
    };
    this.invert = function() {
        var tmp = parseFloat(this.textFiled.text());
        tmp *= -1;
        this.textFiled.text(tmp);
    };
    this.result = function() {
        if (this.mode != 0) {
            this.savedNum2 = parseFloat($('#calcText').text());
        }
        if (this.savedOper == 's') {
            this.savedNum = this.savedNum - this.savedNum2;
        }
        if (this.savedOper == 'a') {
            this.savedNum = this.savedNum + this.savedNum2;
        }
        if (this.savedOper == 'm') {
            this.savedNum = this.savedNum * this.savedNum2;
        }
        if (this.savedOper == 'd') {
            if (this.savedNum2 == 0) return;
            this.savedNum = this.savedNum / this.savedNum2;
        }
        if (this.savedOper == 'd' || this.savedOper == 'm' || this.savedOper == 'a' || this.savedOper == 's') {
            this.textFiled.text(this.savedNum);
        }
        this.usOldButton();
        this.mode = 0;
    };
    this.action = function(oper){
        if(this.mode == 3){
            this.result();
        }
        this.mode = 2;
        this.usOldButton();
        var button = $('#b' + oper);
        button.removeClass('color-lightblue')
        button.addClass('color-black');
        this.savedNum = parseFloat(this.textFiled.text());
        this.savedOper = oper;
    };
    this.append = function(num) {
        if(this.textFiled.text() == '0'){
            this.textFiled.text('');
        }
        if(this.mode == 0 || this.mode == 2) {
            if(num=='.'){
                this.textFiled.text('0.');
            }else {
                this.textFiled.text('');
            }
            this.mode++;
            this.usOldButton();
        }
        if(!(this.textFiled.text().indexOf('.')!=-1 && num=='.')) {
            this.textFiled.append(num);
        }
    };
    this.restart();
    this.bind();
}
