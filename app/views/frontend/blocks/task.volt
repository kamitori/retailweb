<div id="task_list" class="modal fade" role="dialog">
    <div class="modal-dialog left_menu_popup" style="top:0;width:100%;">
        <div class="modal-content" style="background:#a03021;">
            <div class="modal-body">
                <div class="bootbox-body">
                    <div class="row">
                        <ul>
                        	{% for index, item in arr_task %}
                            	<li style="cursor:pointer;float:left;width:50%" onclick="doubleTask('{{item['_id']}}');"><a>{{item['name']}}</a></li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>         
</div>
