
var Login = React.createClass({
    getInitialState: function() {
        return {
            disabled: true,
            username: '',
            password: '',
            loginUrl: ''
        };
    },
    usernameChange: function(event) {
        if(event.target.value && this.state.password){
            this.setState({
                disabled: false,
                username: event.target.value
            });
        }
        else {
            this.setState({
                disabled: true,
                username: event.target.value
            });
        }
    },
    passwordChange: function(event) {
        if(this.state.username && event.target.value){
            this.setState({
                disabled: false,
                password: event.target.value
            });
        }
        else {
            this.setState({
                disabled: true,
                password: event.target.value
            });
        }
    },
    doLogin: function(event) {
        var username = this.state.username;
        var password = this.state.password;
        $("#submit").html('登录中…');

        $.ajax({
            url: '/service/dologin',
            type: 'POST',
            data: {
                username: username,
                password: password
            },
            dataType: 'json',
            cache: false,
            beforeSend: function() {

            },
            success: function(response){
                if(response.code == 0){
                    $("#submit").html('正在跳转中…');
                    location.href = '/service/center';
                }
                else if(response.code == 10001){
                	$("#submit").html('登录');
                	layer.msg(response.message);
                }
                else{
                	$("#submit").html('登录');
                	layer.msg('用户名或者密码错误');
                }
            }.bind(this),
            error: function(XMLHttpRequest, textStatus, errorThrown){
                layer.msg('未知错误');
                $("#submit").html('登录');
            }
        });
    },
    render: function () {
        var username = this.state.username;
        var password = this.state.password;
        var disabled = this.state.disabled;
        return (
            <div className="full-height">
                <div className="weui-cells weui-cells_form weui-cells_form_login login-form">
                    <div className="weui-cell first">
                        <div className="weui-cell__bd">
                            <input className="weui-input" type="text" placeholder="请输入用户名" value={username} onChange={this.usernameChange}/>
                        </div>
                    </div>
                    <div className="weui-cell second">
                        <div className="weui-cell__bd">
                            <input className="weui-input" type="password" placeholder="请输入密码" value={password} onChange={this.passwordChange}/>
                        </div>
                    </div>
                </div>
                <div className="page_login page__bd_spacing">
                    <button className={this.state.disabled ? "submit-now submit-now_disabled" : "submit-now"} disabled={disabled} id="submit" onClick={this.doLogin}>登录</button>
                </div>
            </div>
        );
    }
});

ReactDOM.render(<Login/>, document.getElementById('login-content'));
