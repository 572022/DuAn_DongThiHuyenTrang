using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using PagedList;
using PagedList.Mvc;
using System.IO;
using WebsiteQuanAo.Models;
using System.Data.Entity;
using System.Linq.Dynamic.Core;
using WebsiteQuanAo.Areas.Admin.Models;

namespace WebsiteQuanAo.Areas.Admin.Controllers
{
    public class BillController : Controller
    {
        DataQuanAoDataContext db = new DataQuanAoDataContext();
        // GET: Admin/Bill
        [HttpGet]
        public ActionResult Index(int? size, int? page, string sortProperty, string searchString, string sortOrder = "", int categoryID = 0)
        {
            ViewBag.Keyword = searchString;

            var bill = db.DonHangs.Include(b => b.KhachHang).Include(b => b.ChiTietDonHangs);
            //1.3. Tìm kiếm theo searchString
            if (!String.IsNullOrEmpty(searchString))
                bill = bill.Where(b => b.KhachHang.HoTen.Contains(searchString));

            // 2 Đoạn code này dùng để sắp xếp
            // 2.1. Tạo biến ViewBag gồm sortOrder, searchValue, sortProperty và page
            if (sortOrder == "asc") ViewBag.SortOrder = "desc";
            if (sortOrder == "desc") ViewBag.SortOrder = "";
            if (sortOrder == "") ViewBag.SortOrder = "asc";

            if (String.IsNullOrEmpty(sortProperty)) sortProperty = "KhachHang.HoTen";
            if (categoryID != 0)
                bill = bill.Where(c => c.TinhTrangDonHang == categoryID);
            // Tạo một danh sách mới chứa các giá trị mục 1, 2, 3
            var additionalItems = new List<SelectListItem>
            {
                new SelectListItem { Value = "1", Text = "Đã giao" },
                new SelectListItem { Value = "2", Text = "Đang giao" },
                new SelectListItem { Value = "3", Text = "Đang chờ xác nhận" }
            };

            ViewBag.CategoryID = new SelectList(additionalItems, "Value", "Text");

            // 2.2. Sắp xếp tăng/giảm bằng phương thức OrderBy sử dụng trong thư viện Dynamic LINQ
            if (sortOrder == "desc")
                bill = bill.OrderBy($"{sortProperty} descending"); // Sử dụng đúng cú pháp cho Dynamic LINQ
            else
                bill = bill.OrderBy(sortProperty);

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
            int checkTotal = (int)(bill.ToList().Count / pageSize) + 1;
            // Nếu trang vượt qua tổng số trang thì thiết lập là 1 hoặc tổng số trang
            if (pageNumber > checkTotal) pageNumber = checkTotal;

            // 4. Trả kết quả về Views
            return View(bill.ToPagedList(pageNumber, pageSize));
        }
        public ActionResult Details(int MaHD)
        {
            var donHang = db.DonHangs.SingleOrDefault(dh => dh.MaHD == MaHD);
            var chiTietDonHang = db.ChiTietDonHangs.Where(ct => ct.MaHD == MaHD).ToList();

            if (donHang == null)
            {
                return HttpNotFound(); // Trả về trang lỗi 404 nếu không tìm thấy đơn hàng
            }

            return View(new Bill
            {
                DonHang = donHang,
                chiTietDonHangs=chiTietDonHang
            });
        }
[HttpGet]
        public ActionResult Edit(int MaHD)
        {
            var donHang = db.DonHangs.SingleOrDefault(dh => dh.MaHD == MaHD);
            var chiTietDonHang = db.ChiTietDonHangs.Where(ct => ct.MaHD == MaHD).ToList();

            if (donHang == null)
            {
                return HttpNotFound(); // Trả về trang lỗi 404 nếu không tìm thấy đơn hàng
            }
            var additionalItems = new List<SelectListItem>
            {
                new SelectListItem { Value = "1", Text = "Đã giao" },
                new SelectListItem { Value = "2", Text = "Đang giao" },
                new SelectListItem { Value = "3", Text = "Đang chờ xác nhận" }
            };
            ViewBag.Trangthai = new SelectList(additionalItems, "Value", "Text");
            return View(new Bill
            {
                DonHang = donHang,
                chiTietDonHangs = chiTietDonHang
            });
        }

        [HttpPost]
        [ValidateInput(false)]
        public ActionResult Edit(FormCollection f)
        {
            var donhang = db.DonHangs.SingleOrDefault(n => n.MaHD == int.Parse(f["iMaHD"]));
            var additionalItems = new List<SelectListItem>
            {
                new SelectListItem { Value = "1", Text = "Đã giao" },
                new SelectListItem { Value = "2", Text = "Đang giao" },
                new SelectListItem { Value = "3", Text = "Đang chờ xác nhận" }
            };
            ViewBag.Trangthai = new SelectList(additionalItems, "Value", "Text");
            if (ModelState.IsValid)
            {

                donhang.TinhTrangDonHang = int.Parse(f["Trangthai"]);

                db.SubmitChanges();

                TempData["SuccessMessage"] = "Đơn hàng đã được cập nhật thành công.";
                return RedirectToAction("Index");
            }
            return View(donhang);
        }
    }
}