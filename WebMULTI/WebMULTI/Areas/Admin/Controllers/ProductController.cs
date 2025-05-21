using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using PagedList;
using PagedList.Mvc;
using System.IO;
using WebMULTI.Models;
using System.Data.Entity;
using System.Linq.Dynamic.Core;
//using System.Drawing;
using WebMULTI.Areas.Admin.Models;
using WebsiteQuanAo.Models;
using System.Web.UI.WebControls;

namespace WebMULTI.Areas.Admin.Controllers
{
    public class ProductController : Controller
    {
        webMultiDataContext db = new webMultiDataContext();
        // GET: Admin/Product
        [HttpGet]
        public ActionResult Index(int? size, int? page, string sortProperty, string searchString, string sortOrder = "", int categoryID = 0)
        {
            // 1. Đoạn code dùng để tìm kiếm
            // 1.1. Lưu tư khóa tìm kiếm
            ViewBag.Keyword = searchString;
            //1.2 Lưu chủ đề tìm kiếm
            ViewBag.Subject = categoryID;
            //1.2.Tạo câu truy vấn kết 4 bảng SanPham, DanhMuc, LoaiSP, Color
            var procs = db.SanPhams.Include(b => b.DanhMuc).Include(b => b.LoaiSP);

            //1.3. Tìm kiếm theo searchString
            if (!String.IsNullOrEmpty(searchString))
                procs = procs.Where(b => b.TenSP.Contains(searchString));

            //1.5. Tìm kiếm theo CategoryID
            if (categoryID != 0)
                procs = procs.Where(c => c.MaDM == categoryID);

            ViewBag.CategoryID = new SelectList(db.DanhMucs, "MaDM", "TenDM");
            // 2 Đoạn code này dùng để sắp xếp
            // 2.1. Tạo biến ViewBag gồm sortOrder, searchValue, sortProperty và page
            if (sortOrder == "asc") ViewBag.SortOrder = "desc";
            if (sortOrder == "desc") ViewBag.SortOrder = "";
            if (sortOrder == "") ViewBag.SortOrder = "asc";
            // 2.1. Tạo thuộc tính sắp xếp mặc định là "TenSP"
            if (String.IsNullOrEmpty(sortProperty)) sortProperty = "TenSP";

            // 2.2. Sắp xếp tăng/giảm bằng phương thức OrderBy sử dụng trong thư viện Dynamic LINQ
            if (sortOrder == "desc")
                procs = procs.OrderBy($"{sortProperty} descending"); // Sử dụng đúng cú pháp c Dynamic LINQ
            else
                procs = procs.OrderBy(sortProperty);

            // 3 Đoạn code sau dùng để phân trang
            ViewBag.Page = page;

            // 3.1. Tạo danh sách chọn số trang
            List<SelectListItem> items = new List<SelectListItem>();
            items.Add(new SelectListItem { Text = "5", Value = "5" });
            items.Add(new SelectListItem { Text = "10", Value = "10" });
            items.Add(new SelectListItem { Text = "20", Value = "20" });
            items.Add(new SelectListItem { Text = "25", Value = "25" });
            items.Add(new SelectListItem { Text = "50", Value = "50" });
            items.Add(new SelectListItem { Text = "100", Value = "100" });
            items.Add(new SelectListItem { Text = "200", Value = "200" });
            // 3.2. Thiết lập số trang đang chọn vào danh sách List<SelectListItem> items
            foreach (var item in items)
            {
                if (item.Value == size.ToString()) item.Selected = true;
            }
            ViewBag.Size = items;
            ViewBag.CurrentSize = size;
            // 3.3. Nếu page = null thì đặt lại là 1.
            page = page ?? 1; //if (page == null) page = 1;

            // 3.4. Tạo kích thước trang (pageSize), mặc định là 5.
            int pageSize = (size ?? 5);

            ViewBag.pageSize = pageSize;

            // 3.5. Toán tử ?? trong C# mô tả nếu page khác null thì lấy giá trị page, còn
            // nếu page = null thì lấy giá trị 1 cho biến pageNumber.
            int pageNumber = (page ?? 1);

            // 3.6 Lấy tổng số record chia cho kích thước để biết bao nhiêu trang
            int checkTotal = (int)(procs.ToList().Count / pageSize) + 1;
            // Nếu trang vượt qua tổng số trang thì thiết lập là 1 hoặc tổng số trang
            if (pageNumber > checkTotal) pageNumber = checkTotal;

            // 4. Trả kết quả về Views
            return View(procs.ToPagedList(pageNumber, pageSize));
        }
        public List<ListColor> GetColor()
        {
            // Lấy giỏ hàng từ session và ép kiểu thành List<Cart>.
            List<ListColor> lst = Session["ListColor"] as List<ListColor>;
            if (lst == null)
            {
                lst = new List<ListColor>();
                Session["ListColor"] = lst;
            }
            return lst;
        }
        public ActionResult ListColor()
        {
            List<ListColor> lst = GetColor();

            return View(lst);
        }
        [HttpGet]
        public ActionResult Createimg(int id)
        {           var productImages = db.Productimgs
                    .Where(p => p.MaSP == id)
                    .ToList(); 
            return View(productImages);
        }
        [HttpPost]
        public ActionResult Createimg(int id, Productimg pro, HttpPostedFileBase file1)
        {
           
            if (file1 != null && file1.ContentLength > 0)
            {
                try
                {
                    var sFileName = Path.GetFileName(file1.FileName);
                    var path = Path.Combine(Server.MapPath("~/img"), sFileName);

                    // Kiểm tra xem tệp có tồn tại trên server chưa
                    if (!System.IO.File.Exists(path))
                    {
                        file1.SaveAs(path);
                    }
                    pro.nameimg = sFileName;
                    pro.MaSP = id;
                    db.Productimgs.InsertOnSubmit(pro);  // db là đối tượng DbContext
                    db.SubmitChanges();
                    // Redirect đến trang xem ảnh
                    return RedirectToAction("Createimg");
                }
               
                catch (Exception ex)
                {
                    ViewBag.Error = "Error: " + ex.Message;
                }  
             
            }
            return View();
        }

        [HttpGet]
        public ActionResult CreateColor()
        {
            var colors = db.Colors.ToList();

            // Đặt dữ liệu vào ViewBag
            ViewBag.Colors = new SelectList(colors, "idColor", "Ecolor");
            ViewBag.Colors2 = new SelectList(colors, "idColor", "Ecolor");

            //ViewBag.idColor = new SelectList(db.Colors.ToList().OrderBy(n => n.Ecolor), "idColor", "Ecolor");
            //ViewBag.idColor2 = new SelectList(db.Colors.ToList().OrderBy(n => n.Ecolor), "idColor", "Ecolor");
            return View();
        }
        [HttpPost]
        public ActionResult CreateColor(int id, FormCollection f)
        {
            var colors = db.Colors.ToList();
            ViewBag.Colors = new SelectList(colors, "idColor", "Ecolor");
            ViewBag.Colors2 = new SelectList(colors, "idColor", "Ecolor");
            int color = int.Parse(f["idColor"]);
            int color2 = int.Parse(f["idColor2"]);
            ViewBag.color = color;
            ViewBag.color2 = color2;
            Product_Color co1 = new Product_Color
            {
                idColor = color,
                MaSP = id
            };
            Product_Color co2 = new Product_Color
            {
                idColor = color2,
                MaSP = id
            };

            ViewBag.tb = "Thông báo: Bạn đã chọn thành công màu sắc cho sản phẩm!";
            db.Product_Colors.InsertOnSubmit(co1);
            db.Product_Colors.InsertOnSubmit(co2);

            db.SubmitChanges();

            return View();
        }
        public ActionResult Create()
        {
            ViewBag.MaDM = new SelectList(db.DanhMucs.ToList().OrderBy(n => n.TenDM), "MaDM", "TenDM");
            ViewBag.MaLoai = new SelectList(db.LoaiSPs.ToList().OrderBy(n => n.MaLoai), "MaLoai", "TenLoai");


            return View();
        }

        [HttpPost]
        [ValidateInput(false)]
        public ActionResult Create(Product proc , HttpPostedFileBase fFileUpload)
        {
            
                ViewBag.MaDM = new SelectList(db.DanhMucs.ToList().OrderBy(n => n.TenDM), "MaDM", "TenDM");
                ViewBag.MaLoai = new SelectList(db.LoaiSPs.ToList().OrderBy(n => n.MaLoai), "MaLoai", "TenLoai");
                ViewBag.idColor = new SelectList(db.Colors.ToList().OrderBy(n => n.Ecolor), "idColor", "Ecolor");
                ViewBag.idColor2 = new SelectList(db.Colors.ToList().OrderBy(n => n.Ecolor), "idColor", "Ecolor");

            // Các bước tiếp theo của mã của bạn            
            if (fFileUpload == null)
            {
                ModelState.AddModelError("", "Hãy chọn ảnh bìa.");
                ViewBag.MaDM = new SelectList(db.DanhMucs.ToList().OrderBy(n => n.TenDM), "MaDM", "TenDM");
                ViewBag.MaLoai = new SelectList(db.LoaiSPs.ToList().OrderBy(n => n.MaLoai), "MaLoai", "TenLoai");
                ViewBag.idColor = new SelectList(db.Colors.ToList().OrderBy(n => n.nameColor), "id", "Ecolor");
                ViewBag.idColor2 = new SelectList(db.Colors.ToList().OrderBy(n => n.nameColor), "idColor", "Ecolor");

            }
            else
            {
                if (ModelState.IsValid)
                {
                    var sFileName = Path.GetFileName(fFileUpload.FileName);
                    var path = Path.Combine(Server.MapPath("~/img"), sFileName);

                    // Kiểm tra xem tệp có tồn tại trên server chưa
                    if (!System.IO.File.Exists(path))
                    {
                        fFileUpload.SaveAs(path);
                    }
                    proc.AnhBia = sFileName;
                    var procduct = new SanPham
                    {
                        TenSP = proc.TenSP,
                        MaLoai = proc.MaLoai,
                        MaDM = proc.MaDM,
                        Size = Convert.ToChar(proc.Size),
                        Mota = "fffff",

                        AnhBia = proc.AnhBia,
                        //idColor = proc.idColor,
                        GiaSP = proc.GiaSP,
                        NgayNhap = Convert.ToDateTime(DateTime.Now),
                        SoLuongBan = 0,
                        SoLuongTon = proc.SoLuongTon,

                    };
                    db.SanPhams.InsertOnSubmit(procduct);
                    db.SubmitChanges();

                    TempData["SuccessMessage"] = "Sản phẩm đã được thêm thành công.";
                    //CKinder.FileBrowser _FileBrowser = new CKFinder.FileBrowser();
                    //_FileBrowser.BasePath = "../ckfinder/";
                    return RedirectToAction("Index", "Product");
                }


            }
                return View();
            
           
        }

        public ActionResult Details(int id)
        {
            var proc = db.SanPhams.SingleOrDefault(n => n.MaSP == id);
            if (proc == null)
            {
                Response.StatusCode = 404;
                return null;
            }
            var colors = db.Product_Colors
        .Where(pc => pc.MaSP == id)
        .Join(db.Colors, pc => pc.idColor, c => c.idColor, (pc, c) => c)
        .ToList();

            // Thêm danh sách màu sắc vào ViewBag để sử dụng trong view
            ViewBag.Colors = colors;
            return View(proc);

        }
        [HttpGet]
        public ActionResult Delete(int id)
        {
            var proc = db.SanPhams.SingleOrDefault(n => n.MaSP == id);
            if (proc == null)
            {
                Response.StatusCode = 404;
                return null;
            }
            return View(proc);
        }
        [HttpPost, ActionName("Delete")]
        public ActionResult DeleteConfirm(int id)
        {
            var proc = db.SanPhams.SingleOrDefault(n => n.MaSP == id);
            if (proc == null)
            {
                Response.StatusCode = 404;
                return null;
            }
            var ctdh = db.ChiTietDonHangs.Where(ct => ct.MaSP == id);
            if (ctdh.Count() > 0)
            {
                ViewBag.ThongBao = "Sản phẩm này đang có trong bảng Chi tiết đặt hàng <br>" +
                    "Nếu muốn xóa thì phải xóa hết mã sản phẩm này trong bảng Chi tiết đặt hàng";
                return View(proc);
            }
            var binhluan = db.BinhLuans.Where(bl => bl.MaSP == id).ToList();
            if (binhluan != null)
            {
                db.BinhLuans.DeleteAllOnSubmit(binhluan);
                db.SubmitChanges();
            }
            db.SanPhams.DeleteOnSubmit(proc);
            db.SubmitChanges();
            TempData["SuccessMessage"] = "Sản phẩm đã được xóa thành công.";
            return RedirectToAction("Index");
        }
        [HttpGet]
        public ActionResult Edit(int id)
        {
            var product = db.SanPhams.SingleOrDefault(n => n.MaSP == id);
            if (product == null)
            {
                Response.StatusCode = 404;
                return null;
            }
            ViewBag.MaDM = new SelectList(db.DanhMucs.ToList().OrderBy(n => n.TenDM), "MaDM", "TenDM", product.MaDM);
            ViewBag.MaLoai = new SelectList(db.LoaiSPs.ToList().OrderBy(n => n.MaLoai), "MaLoai", "TenLoai", product.MaLoai);
            //ViewBag.idColor = new SelectList(db.Colors.ToList().OrderBy(n => n.nameColor), "idColor", "nameColor", product.idColor);

            return View(product);
        }

        [HttpPost]
        [ValidateInput(false)]
        public ActionResult Edit(FormCollection f, HttpPostedFileBase fFileUpload)
        {
           
                var proc = db.SanPhams.SingleOrDefault(n => n.MaSP == int.Parse(f["iMaSP"]));
                ViewBag.MaDM = new SelectList(db.DanhMucs.ToList().OrderBy(n => n.TenDM), "MaDM", "TenDM", proc.MaDM);
                ViewBag.MaLoai = new SelectList(db.LoaiSPs.ToList().OrderBy(n => n.MaLoai), "MaLoai", "TenLoai", proc.MaLoai);
                //ViewBag.idColor = new SelectList(db.Colors.ToList().OrderBy(n => n.nameColor), "idColor", "nameColor", proc.idColor);
                if (ModelState.IsValid)
                {
                    if (fFileUpload != null)
                    {
                        var sFileName = Path.GetFileName(fFileUpload.FileName);
                        var path = Path.Combine(Server.MapPath("~/img"), sFileName);
                        if (!System.IO.File.Exists(path))
                        {
                            fFileUpload.SaveAs(path);
                        }
                        proc.AnhBia = sFileName;
                    }
                    proc.TenSP = f["sTenSP"];
                    proc.MaLoai = int.Parse(f["MaLoai"]);
                    proc.MaDM = int.Parse(f["MaDM"]);
                    proc.Size = Convert.ToChar(f["sSize"]);
                    proc.Mota = f["sMoTa"];
                    //proc.idColor = int.Parse(f["idColor"]);
                    proc.GiaSP = int.Parse(f["mGiaBan"]);
                    proc.SoLuongTon = int.Parse(f["iSoLuong"]);
                    proc.NgayNhap = Convert.ToDateTime(f["dNgayNhap"]);

                    db.SubmitChanges();

                    TempData["SuccessMessage"] = "Sản phẩm đã được cập nhật thành công.";
                    //CKFinder.FileBrowser _FileBrowser = new CKFinder.FileBrowser();
                    //_FileBrowser.BasePath = "../ckfinder/";
                    return RedirectToAction("Index");
                }
                return View(proc);
            }
          
        }

        //[HttpGet]
        //public ActionResult Editimg(int id)
        //{
        //    var product = db.Productimgs.SingleOrDefault(n => n.MaSP == id);
        //    if (product == null)
        //    {
        //        Response.StatusCode = 404;
        //        return null;
        //    }
        //    return View(product);
        //}
        //[HttpPost]
        //[ValidateInput(false)]
        //public ActionResult Editimg(int id, FormCollection f, HttpPostedFileBase fFileUpload)
        //{
        //    var proc = db.SanPhams.SingleOrDefault(n => n.MaSP == id);
        //    if (ModelState.IsValid)
        //    {
        //        if (fFileUpload != null)
        //        {
        //            var sFileName = Path.GetFileName(fFileUpload.FileName);
        //            var path = Path.Combine(Server.MapPath("~/img"), sFileName);
        //            if (!System.IO.File.Exists(path))
        //            {
        //                fFileUpload.SaveAs(path);
        //            }
        //            proc.AnhBia = sFileName;
        //        }

        //        db.SubmitChanges();

        //        TempData["SuccessMessage"] = "Sản phẩm đã được cập nhật thành công.";
        //        //CKFinder.FileBrowser _FileBrowser = new CKFinder.FileBrowser();
        //        //_FileBrowser.BasePath = "../ckfinder/";
        //        return RedirectToAction("Index");
        //    }
        //    return View(proc);
        //}
     
}