/*prompt 提示条*/
/*success fail 两种状态 */
function prompt(panent,type,message) {
    let prompt = `
        <div class="prompt">
            <div class="message-warp ${type}">
                <span class="message">${message}</span>
            </div>
        </div>
    `;
    $(`.${panent}`).append(prompt)
    $('.prompt').stop(true).fadeOut(3000,function() {
        $('.prompt').remove()
    })
}
/* model提示框 */
/* success fail 两种框框 */
function model(panent,type,message,callback) {
    let type_class = type === "fail" ? 'font_notice' : 'font_succ';
    let type_btn = type === "fail" ? `<button type="button" class="btn nov-btn-cancle" data-dismiss="modal">NO</button>
    <button type="button" class="btn nov-btn-update delete-click" data-dismiss="modal">YES</button>` : '<button type="button" class="btn nov-btn-update" data-dismiss="modal">YES</button>'
    let model = `
        <div class="modal fade ${type}_modal" tabindex="-1" role="dialog" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title"> </h4>
                    </div>
                    <div class="modal-body" style="text-align: center">
                        <i class="${type_class}"></i>
                        <h2>${message}</h2>
                    </div>
                    <div class="modal-footer">
                        ${type_btn}
                    </div>
                </div>
            </div>
        </div>
    `;
    $(`.${panent}`).append(model);
    callback();
    $(`.${type}_modal`).on('hidden.bs.modal', function (e) {
        $(`.${type}_modal`).remove()
    })
}