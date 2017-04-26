
var NumChange = React.createClass({
    getInitialState: function() {
        return {
        	decreaseDisabled: true,
        	increaseDisabled: false,
            num: 1,
            focusNum: 1
        };
    },
    decreaseChange: function(total, event) {
    	if(total >= 1){
	    	var num = Number(this.state.num);
	        if(num <= 1){
	            this.setState({
	            	decreaseDisabled: true,
	            	num: num,
	            	focusNum: num
	            });
	        }
	        else {
	        	var new_num = num - 1;
	        	if(new_num > 1){
	        		var temp = false;
	        	}
	        	else{
	        		var temp = true;
	        	}
	        	
	            this.setState({
	            	decreaseDisabled: temp,
	            	increaseDisabled: false,
	                num: new_num,
	            	focusNum: num
	            });
	        }
    	}
    	else{
    		this.setState({
            	decreaseDisabled: true,
            	increaseDisabled: true,
            	num: 0,
            	focusNum: num
            });
    	}
    },
    increaseChange: function(total, event) {
    	if(total >= 1){
    		var num = Number(this.state.num);
	        if(num < total){
	        	var new_num = num + 1;
	        	if(new_num < total){
	        		var temp = false;
	        	}
	        	else{
	        		var temp = true;
	        	}
	        	
	            this.setState({
	            	increaseDisabled: temp,
	            	decreaseDisabled: false,
	                num: new_num,
	            	focusNum: num
	            });
	        }
	        else {
	            this.setState({
	            	increaseDisabled: true,
	                num: num,
	            	focusNum: num
	            });
	        }
    	}
    	else{
    		this.setState({
            	decreaseDisabled: true,
            	increaseDisabled: true,
            	num: 0,
            	focusNum: num
            });
    	}
    },
    numChange: function(total, event) {
    	var inputNum = Number(event.target.value);
    	if(inputNum >= 1 && inputNum <= total){
    		if(inputNum == total){
    			var increaseTemp = true;
    		}
    		else{
    			var increaseTemp = false;
    		}
    		
    		if(inputNum > 1){
    			var decreaseTemp = false;
    		}
    		else{
    			var decreaseTemp = true;
    		}
    	}
    	else{
    		var increaseTemp = true;
    		var decreaseTemp = true;
    	}
    	
    	this.setState({
        	increaseDisabled: increaseTemp,
        	decreaseDisabled: decreaseTemp,
            num: event.target.value
        });
    },
    numKeyUp: function(total, event) {
    	var inputNum = Number(event.target.value);
    	var focusNum = this.state.focusNum;
    	if(inputNum > total){
    		if(focusNum >= total){
    			var increaseTemp = true;
    		}
    		else{
    			var increaseTemp = false;
    		}
    		
    		if(focusNum > 1){
    			var decreaseTemp = false;
    		}
    		else{
    			var decreaseTemp = true;
    		}
    		
    		this.setState({
    			increaseDisabled: increaseTemp,
            	decreaseDisabled: decreaseTemp,
    			num: focusNum
            });
    		layer.msg('不在合法购买数量以内');
    	}
    },
    numFocus: function(total, event) {
    	this.setState({
    		focusNum: this.state.num
        });
    },
    
    render: function () {
        var num = this.state.num;
        var total = $("#num-change").attr('total');
        var numInputStyle = {
        	border: 'none',
        	outline: '0px solid white',
        	width: '60px',
        	height: '35px',
        	textAlign: 'center'
        };
        var numOuterStyle = {
        	padding: '0px'
        };
        
        return (
        	<div className="goods-num-content">
                <span className="goods-num-item-desc">数量：</span>
                <span className={this.state.decreaseDisabled ? "goods-num-item disabled" : "goods-num-item"} onClick={this.decreaseChange.bind(this, total)}>-</span>
                <span className="goods-num-item" style={numOuterStyle}><input type="number" value={num} id="num" onFocus={this.numFocus.bind(this, total)} onKeyUp={this.numKeyUp.bind(this, total)} onChange={this.numChange.bind(this, total)} style={numInputStyle}/></span>
                <span className={this.state.increaseDisabled ? "goods-num-item disabled last" : "goods-num-item last"} onClick={this.increaseChange.bind(this, total)}>+</span>
                <span className="goods-num-item-have">库存：{total}</span>
                <div className="clear"></div>
            </div>
        );
    }
});

ReactDOM.render(<NumChange/>, document.getElementById('num-change'));
