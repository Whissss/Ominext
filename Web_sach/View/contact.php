<div class="contact">
    <div class="row">
                <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                Tên</label>
                            <input type="text" class="form-control" id="name" placeholder="Vui Lòng Nhập Tên" required="required" />
                        </div>
                        <div class="form-group">
                            <label for="email">
                                Địa Chỉ Email</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                </span>
                                <input type="email" class="form-control" id="email" placeholder="Vui Lòng Điền Địa Chỉ Email" required="required" /></div>
                        </div>
                        <div class="form-group">
                            <label for="subject">
                                Vấn Đề</label>
                            <select id="subject" name="subject" class="form-control" required="required">
                                <option value="" selected="">Chọn 1:</option>
                                <option value="Tai_Khoan">Tài Khoản</option>
                                <option value="San_Pham">Sản Phẩm</option>
                                <option value="Khac">Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                Tin Nhắn</label>
                            <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required"
                                placeholder="Nội Dung Tin Nhắn"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary pull-right" id="btnContactUs">
                            Gửi</button>
                    </div>
                </div>
                </form>
    </div>
</div>
