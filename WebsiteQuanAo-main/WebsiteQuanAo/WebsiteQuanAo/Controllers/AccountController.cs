using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Linq.Dynamic.Core;
using System.Linq.Dynamic.Core.Tokenizer;
using System.Web;
using System.Web.Mvc;
using System.Web.Mvc.Html;
using WebsiteQuanAo.Models;
using static System.Collections.Specialized.BitVector32;

namespace WebsiteQuanAo.Controllers
{
    public class AccountController : Controller
    {
        // GET: Account
        private DataQuanAoDataContext db = new DataQuanAoDataContext();

        public ActionResult Register()
        {
            return View();
        }

        [HttpPost]
        public ActionResult Register(User model)
        {
            if (ModelState.IsValid)
            {
                if (db.KhachHangs.Any(t => t.Username == model.Username))
                {
                    ModelState.AddModelError("Username", "Tên đăng nhập đã tồn tại.");
                    return View(model);
                }
                if (db.KhachHangs.Any(t => t.Password == model.Password))
                {
                    ModelState.AddModelError("Password", "Mật khẩu yếu, vui lòng đổi mật khẩu khác.");
                    return View(model);
                }
                if (db.KhachHangs.Any(k => k.Email == model.Email))
                {
                    ModelState.AddModelError("Email", "Email đã tồn tại.");
                    return View(model);
                }
                if (db.KhachHangs.Any(k => k.SDT == model.PhoneNum))
                {
                    ModelState.AddModelError("PhonNum", "Số điện thoại đã tồn tại.");
                    return View(model);
                }
                var khachHang = new KhachHang
                {
                    HoTen = model.Fullname,
                    GioiTinh = model.Gender,
                    DiaChi = model.Address,
                    Email = model.Email,
                    SDT = model.PhoneNum,
                    Username = model.Username,
                    Password = model.Password
                };
                db.KhachHangs.InsertOnSubmit(khachHang);
                db.SubmitChanges();
                return RedirectToAction("Login", "Account");
            }
            return View(model);
        }

        public ActionResult LogIn()
        {
            return View();
        }
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult LogIn(string username, string password)
        {
            if (ModelState.IsValid)
            {
                var user = db.KhachHangs.Where(s => s.Username.Equals(username) && s.Password.Equals(password)).ToList();
                if (user.Count() > 0)
                {
                    if (user != null)
                    {

                        Session["Username"] = user.FirstOrDefault().Username;
                        return RedirectToAction("Home", "Clothes");
                    }
                    else
                    {
                        ViewBag.Error = "Tên đăng nhập hoặc mật khẩu không đúng.";
                        return RedirectToAction("LogIn");
                    }
                }
            }
            return View();
        }

        public ActionResult LogOut()
        {
            Session.Remove("Username");
            return RedirectToAction("Home", "Clothes");
        }
        public ActionResult MyAccount()
        {
            try
            {
                var user = (from s in db.KhachHangs where s.Username == Session["Username"].ToString() select s).SingleOrDefault();
                if (user != null)
                {
                    return View(user);
                }
                else
                {
                    return RedirectToAction("LogIn");
                }
            }
            catch { return RedirectToAction("LogIn"); }

        }

        [HttpGet]
        public ActionResult ChangePass()
        {
            return View();
        }
        [HttpPost]
        //[ValidateInput(false)]
        public ActionResult ChangePass(FormCollection f)
        {
            var user = (from s in db.KhachHangs where s.Username == Session["Username"].ToString() select s).SingleOrDefault();

            if (ModelState.IsValid)
            {
                if (f["NewPass"] == f["CormNewPass"] && f["CurPass"] == user.Password)
                {
 
                    user.HoTen = f["hoten"];
                    user.Username = f["username"];
                    user.Email = f["email"];
                    user.Password = f["NewPass"];
                }

                db.SubmitChanges();
                TempData["SuccessMessage"] = "Mật khẩu đã được cập nhật thành công.";
                return RedirectToAction("MyAccount");
            }
            return View(user);
        }


        public ActionResult qldonhang()
        {
            var user1 = (from s in db.KhachHangs where s.Username == Session["Username"].ToString() select s).SingleOrDefault();
            //từ tên tài khoản tìm makh, lọc đơn hàng từ makh đã tạo
            int makh = user1.MaKH;
            var clothes2 = from c2 in db.DonHangs where c2.MaKH == makh select c2;

            return PartialView(clothes2);
        }
        public ActionResult Chitiet(int iMaHD)
        {
            //var cthd= from ct in db.ChiTietDonHangs where ct.MaHD == iMaHD select ct;
            using (var db = new DataQuanAoDataContext())
            {
                var query = (from sp in db.SanPhams
                             join cthd in db.ChiTietDonHangs on sp.MaSP equals cthd.MaSP
                             join hd in db.DonHangs on cthd.MaHD equals hd.MaHD
                             where hd.MaHD == iMaHD
                             select new DonHang_CT
                             {
                                MaHD=hd.MaHD,
                                 MaSP=sp.MaSP,
                                 TongTien=(int)hd.TongTien,
                                 NgayMua = (DateTime)hd.NgayMua,
                                 DaThanhToan=(bool)hd.DaThanhToan,
                                 TinhTrangDonHang =(int)hd.TinhTrangDonHang,
                                 TenSanPhams =db.ChiTietDonHangs.Where(ct=>ct.MaHD== iMaHD).Join(db.SanPhams, cthd => cthd.MaSP, sp => sp.MaSP, (ct, sp) => sp.TenSP).ToList()
                             }).FirstOrDefault();
                return PartialView(query);
            }        
        }
      public ActionResult from_address(int iMaHD)
        {
            //infAddress formData = Session["FormData"] as infAddress;

            //if (formData != null)
            //{
            //  return View(formData ?? new infAddress());
            //}
            //else
            //{
            // return View();
            //}
            using (var db = new DataQuanAoDataContext())
            {
                var query = (from cthd in db.ChiTietDonHangs
                             join kh in db.KhachHangs on cthd.MaKH equals kh.MaKH
                             
                             where cthd.MaHD == iMaHD
                             select new KH_CT
                             {
                                 MaHD = cthd.MaHD,
                                 MaKH = kh.MaKH,
                                 HoTen=kh.HoTen,
                                 Diachi=kh.DiaChi,
                                 sdt= kh.SDT
                             }).FirstOrDefault();
                return View(query);
            }
        }

    }
}