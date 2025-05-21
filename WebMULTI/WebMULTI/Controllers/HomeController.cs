using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using WebMULTI.Models;
using PagedList;
using PagedList.Mvc;
using System.Xml.Linq;
using System.Web.UI;
using System.Drawing;
using WebsiteQuanAo.Models;
using System.Web.UI.WebControls;

namespace WebMULTI.Controllers
{
    public class HomeController : Controller
    {
        webMultiDataContext db = new webMultiDataContext();
        private List<SanPham> newproduct(int count)
        {
            return db.SanPhams.OrderByDescending(a => a.NgayNhap).Take(count).ToList();
        }
        private List<SanPham> bestSellers(int count)
        {
            return db.SanPhams.OrderByDescending(a => a.SoLuongBan).Take(count).ToList();
        }

        public ActionResult Product(int? page)
        {
            int iPageNum = (page ?? 1);
            int iPageSize = 8;
            return PartialView(db.SanPhams.ToList().OrderByDescending(s => s.NgayNhap).ToPagedList(iPageNum, iPageSize));
        }
        public ActionResult BestSellers()
        {
            var listbestsellers = bestSellers(8);
            return PartialView("_layoutpd", listbestsellers);
        }
        public ActionResult Maybeyoulike()
        {
            var listbestsellers = newproduct(4);
            return PartialView("_layoutpd", listbestsellers);
        }
        public ActionResult PartialNav()
        {        
            return PartialView("_PartialTopbar_Nav");
        }
     
        public ActionResult pro_color(int id)
        {
            using (var db = new webMultiDataContext())
            {
               var colorsForProduct = (
              from pc in db.Product_Colors
              join c in db.Colors on pc.idColor equals c.idColor
              join sp in db.SanPhams on pc.MaSP equals sp.MaSP
              where pc.MaSP == id
              select new pro_color
              {
                  namecolor = c.nameColor,
                  MaSP=id,
                  Name=sp.Mota
              }
          ).ToList();
                return PartialView("_filterColor", colorsForProduct);
            }
        }

        public List<pro_color> GetProcductDetails()
        {
            List<pro_color> procductDetails = Session["ProcductDetails"] as List<pro_color>;
            if (procductDetails == null)
            {
                procductDetails = new List<pro_color>();
                Session["ProcductDetails"] = procductDetails;
            }
            return procductDetails;
        }
        public ActionResult detail(int id, int? page)
        {
            // Lọc danh sách màu sắc dựa trên mã sản phẩm
            var query = (from pc in db.Product_Colors
                         join c in db.Colors on pc.idColor equals c.idColor
                         where pc.MaSP == id
                         select new
                         {
                             id = c.idColor,
                             TenColor = c.nameColor
                         }).ToList();
            ViewBag.MaCD = new SelectList(query, "TenColor", "TenColor");
            var product = db.SanPhams.FirstOrDefault(p => p.MaSP == id);

            if (product == null)
            {
                return HttpNotFound();
            }
          
            var danhmuc = db.DanhMucs.FirstOrDefault(dm => dm.MaDM == product.MaDM);
            var loaisp = db.LoaiSPs.FirstOrDefault(loai => loai.MaLoai == product.MaLoai);
            var binhLuans = db.BinhLuans.Where(bl => bl.MaSP == id).ToList();
           

            // Lấy danh sách bình luận và phân trang
            int pageSize = 4; // Số lượng bình luận trên mỗi trang
            int pageNumber = (page ?? 1);
            var comments = db.BinhLuans.Where(c => c.MaSP == id).OrderByDescending(c => c.NgayBL).ToPagedList<WebMULTI.Models.BinhLuan>(pageNumber, pageSize);       
            // Kiểm tra người dùng đã bình luận vào sản phẩm chưa
            string user = Session["Username"] as string;
            bool hasComment = db.BinhLuans.Any(c => c.MaSP == id && c.Username == user);
            ViewBag.HasComment = hasComment;

            // Lưu thông tin sản phẩm vào Session để sử dụng cho phần bình luận
            Session["Product"] = product;
            var productdetails = new pro_color
            {
                SanPham = product,
                BinhLuans = binhLuans,
               
                DanhMuc = danhmuc,
                LoaiSP = loaisp,
                ProductId = id,
            };
            return View(new Procom
            {
                procductDetails = productdetails,
                Comments = comments
            });
        }

        public ActionResult partialImg(int masp)
        {
           var img = from c2 in db.Productimgs where c2.MaSP == masp select c2;
            return PartialView("_partialImg", img);
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
            return RedirectToAction("detail", new { id = productId });
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

        //end comment
        public ActionResult Index()
        {
            return View();
        }
        public ActionResult Carousel()
        {
            return PartialView();
        }


        public ActionResult About()
        {
            ViewBag.Message = "Your application description page.";

            return View();
        }

        public ActionResult Contact()
        {
            ViewBag.Message = "Your contact page.";

            return View();
        }
        // lọc theo loại danh mục
        public ActionResult categories(int maloai, int madm)
        {
            var clothes = from c in db.SanPhams where c.MaLoai == maloai && c.MaDM==madm select c;

            return PartialView(clothes);            
        }
     
        [HttpGet]
        public ActionResult Search(string content)
        {
            ViewBag.content_search = "Kết quả tìm kiếm cho " + content;
            var search = from s in db.SanPhams
                         where s.TenSP == content
                         select s;
            var search1 = db.SanPhams.Where(s => s.TenSP.ToLower().Contains(content.ToLower())).ToList();
            if (search1.Count() == 0)
            {
                ViewBag.ThongBaosearch = "Không tìm thấy sản phẩm";
            }
            return View("categories", search1);
        }
        private Search_price model = new Search_price();
        public ActionResult Search_price1()
        {
            List<SanPham> pro = model.GetAllProducts();
            return View(pro);
        }
        [HttpPost]
        public ActionResult FilterByPrice(double min, double max)
        {
            List<SanPham> filteredProducts = model.FilterProductsByPriceRange(min, max);
            return View("Index", filteredProducts);
        }
        //lọc sản phẩm theo màu
        public ActionResult Filter_colorPartial()
        {
            var color = from co in db.Colors select co;
            return PartialView(color);
        }
        public ActionResult Filter_color(int id,string nameColor)
        {
            var search = (from s in db.Product_Colors
                         join sp in db.SanPhams on s.MaSP equals sp.MaSP
                         where s.idColor == id
                         select sp).ToList();
            // select sp thì sẽ hiển thị sản phẩm
            ViewBag.nameColor ="Hiển thị sản phẩm theo màu "+nameColor;
            return View("categories", search);
        }
        // lọc theo giá 
        public ActionResult btnFilter(int? id)
        {
            if (id == 1)
            {
                ViewBag.active = 1;
                ViewBag.title_type = "Sản phẩm mới nhất";
                var clothes = (from c in db.SanPhams select c).OrderByDescending(x => x.NgayNhap).ToList();
                return View("categories", clothes);
            }
            if (id == 2)
            {
                ViewBag.active = 2;
                ViewBag.title_type = "Thứ tự đánh giá: Thấp đến cao";
                var clothes = (from c in db.SanPhams select c).OrderBy(x => x.GiaSP).ToList();
                return View("categories", clothes);
            }

            else
            {
                ViewBag.active = 3;
                ViewBag.title_type = "Thứ tự đánh giá: Cao đến thấp";
                var clothes = (from c in db.SanPhams select c).OrderByDescending(x => x.GiaSP).ToList();
                return View("categories", clothes);
            }

        }
    }
}