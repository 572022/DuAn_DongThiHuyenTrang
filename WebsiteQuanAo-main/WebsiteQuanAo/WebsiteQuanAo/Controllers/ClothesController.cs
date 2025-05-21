using Microsoft.Ajax.Utilities;
using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Data.Entity.Infrastructure;
using System.Data.SqlTypes;
using System.Diagnostics;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using WebsiteQuanAo.Models;
using PagedList;
using PagedList.Mvc;
using System.Web.UI;

namespace WebsiteQuanAo.Controllers
{
    public class ClothesController : Controller
    {
        //private string connection;
        //private Clothes_linqDataContext db;
        //public ClothesController()
        //{// khởi tạo chuỗi kết nối
        //    connection = "Data Source=DESKTOP-E44HG9G\\SQLEXPRESS;Initial Catalog=WebsiteQuanAo;Integrated Security=True";
        //    db = new Clothes_linqDataContext(connection);
        //}
        DataQuanAoDataContext db=new DataQuanAoDataContext();
        public ActionResult FooterPartial()
        {
            return PartialView();
        }
        public ActionResult _NavPartial()
        {
            return PartialView();
        }
        private List<SanPham> newproduct(int count)
        {
            return db.SanPhams.OrderByDescending(a => a.NgayNhap).Take(count).ToList();
        }
        private List<SanPham> bestSellers(int count)
        {
            return db.SanPhams.OrderByDescending(a => a.SoLuongBan).Take(count).ToList();
        }
        // GET: Clothes
        public ActionResult Home(int ? page)
        {
            int iPageNum = (page ?? 1);
            int iPageSize = 6;
            return PartialView(db.SanPhams.ToList().OrderBy(s => s.MaSP).ToPagedList(iPageNum, iPageSize));
            //return View();
        }
        public ActionResult NewProduct()
        {

            var listNewProduct = newproduct(8);
            return PartialView("_list_product_home", listNewProduct);
        }
        public ActionResult BestSellers()
        {
            var listbestsellers = bestSellers(8);
            return PartialView("_list_product_home", listbestsellers);
        }
        public ActionResult clothes_Type(int? id, int id2)
        {
            if (id2 != 0)
            {
                ViewBag.title_type = "";
                var clothes2 = from c2 in db.SanPhams where c2.MaLoai == id2 select c2;
                return PartialView(clothes2);

            }
            else
            {
                ViewBag.title_type = "";
                var clothes1 = from c in db.SanPhams where c.MaDM == id select c;
                return PartialView(clothes1);

            }
        }
       
        public ActionResult clothes_Type_loai(int id)
        {
            var clothes = from c in db.SanPhams where c.MaLoai == id select c;

            return PartialView(clothes);
        }
        public ActionResult Filter_colorPartial()
        {
            var color = from co in db.Colors select co;
            return PartialView(color);
        }
  
        public ActionResult ProductComments()
        {
            using (var db = new DataQuanAoDataContext()) 
            {
                var query = from product in db.SanPhams
                            join comment in db.BinhLuans on product.MaSP equals comment.MaSP
                            select new ProductCommentViewModel
                            {
                                Anhbia = product.AnhBia,
                                CommentText = comment.Danhgia,
                                User = comment.Username,
                                tensp = product.TenSP,
                                Ngaybl= (DateTime)comment.NgayBL
                            };
                
                var productsWithComments = query.OrderBy(x => x.Ngaybl).Take(5).ToList();
                return View(productsWithComments);
            }
        }
        //          search

        [HttpGet]
        public ActionResult Search(string content)
        {
            ViewBag.content = "Kết quả tìm kiếm cho "+ content;
            var search = from s in db.SanPhams
                         where s.TenSP == content
                         select s;
            var search1 = db.SanPhams.Where(s => s.TenSP.ToLower().Contains(content.ToLower())).ToList();
            if (search1.Count() == 0)
            {
                ViewBag.ThongBaosearch = "Không tìm thấy sản phẩm";
            }
            return View("clothes_Type",search1);
        }
           
        public ActionResult btnFilter(int ?id)
        {
            if (id == 1)
            {
                ViewBag.active = 1;
                ViewBag.title_type = "Sản phẩm mới nhất";
             var clothes = (from c in db.SanPhams select c).OrderByDescending(x => x.NgayNhap).ToList();
             return View("clothes_Type", clothes);
            }
            if (id == 2)
            {
                ViewBag.active = 2;
                ViewBag.title_type = "Thứ tự đánh giá: Thấp đến cao";
                var clothes = (from c in db.SanPhams select c).OrderBy(x => x.GiaSP).ToList();
                return View("clothes_Type", clothes);
            }
          
            else
            {
                ViewBag.active = 3;
                ViewBag.title_type = "Thứ tự đánh giá: Cao đến thấp";
                var clothes = (from c in db.SanPhams select c).OrderByDescending(x => x.GiaSP).ToList();
                return View("clothes_Type", clothes);
            }

        }
        public ActionResult Filter_color(int id, string nameColor )
        {
           
            var fc = from s in db.SanPhams
                         where s.idColor== id
                         select s;
            if (fc.Count() == 0)
            {
                ViewBag.title_type = "Không tìm thấy sản phẩm có màu "+ nameColor;
            }
            else
            {
                ViewBag.title_type = nameColor;
            }
            return View("clothes_Type", fc);
        }
        //----------------khanh-------------
        public List<ProcductDetails> GetProcductDetails()
        {
            List<ProcductDetails> procductDetails = Session["ProcductDetails"] as List<ProcductDetails>;
            if (procductDetails == null)
            {
                procductDetails = new List<ProcductDetails>();
                Session["ProcductDetails"] = procductDetails;
            }
            return procductDetails;
        }
        public ActionResult DetailsProduct(int id, int? page)
        {
            var product = db.SanPhams.FirstOrDefault(p => p.MaSP == id);

            if (product == null)
            {
                return HttpNotFound();
            }

            var color = db.Colors.FirstOrDefault(c => c.idColor == product.idColor);
            var danhmuc = db.DanhMucs.FirstOrDefault(dm => dm.MaDM == product.MaDM);
            var loaisp = db.LoaiSPs.FirstOrDefault(loai => loai.MaLoai == product.MaLoai);
            var binhLuans = db.BinhLuans.Where(bl => bl.MaSP == id).ToList();

            // Lấy danh sách bình luận và phân trang
            int pageSize = 4; // Số lượng bình luận trên mỗi trang
            int pageNumber = (page ?? 1);
            var comments = db.BinhLuans.Where(c => c.MaSP == id).OrderByDescending(c => c.NgayBL).ToPagedList<WebsiteQuanAo.Models.BinhLuan>(pageNumber, pageSize);            // Kiểm tra người dùng đã bình luận vào sản phẩm chưa
            string user = Session["Username"] as string;
            bool hasComment = db.BinhLuans.Any(c => c.MaSP == id && c.Username == user);
            ViewBag.HasComment = hasComment;

            // Lưu thông tin sản phẩm vào Session để sử dụng cho phần bình luận
            Session["Product"] = product;
            var productdetails = new ProcductDetails
            {
                SanPham = product,
                BinhLuans = binhLuans,
                Color = color,
                DanhMuc = danhmuc,
                LoaiSP = loaisp,
                ProductId = id,
            };
            return View(new ProCom
            {
                procductDetails = productdetails,
                Comments = comments
            });
        }



        [HttpPost]
        public ActionResult AddComment(int productId, string comment)
        {
            string user = Session["Username"] as string;
            string selectedRating = Request.Form["Rating"];

            // Tạo một bình luận mới
            var newComment = new BinhLuan
            {
                MaSP = productId,
                Username = user,
                NgayBL = DateTime.Now,
                Danhgia = selectedRating,
                Noidung = comment
            };

            db.BinhLuans.InsertOnSubmit(newComment);
            db.SubmitChanges();

            var commentedProducts = GetCommentedProducts();
            commentedProducts.Add(productId);
            return RedirectToAction("DetailsProduct", new { id = productId });
        }

        private List<int> GetCommentedProducts()
        {
            List<int> commentedProducts = Session["CommentedProducts"] as List<int>;
            if (commentedProducts == null)
            {
                commentedProducts = new List<int>();
                Session["CommentedProducts"] = commentedProducts;
            }
            return commentedProducts;
        }


    }
}
