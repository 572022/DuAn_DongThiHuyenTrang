using System;
using System.Collections.Generic;
using System.Globalization;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using WebsiteQuanAo.Areas.Admin.Models;
using WebsiteQuanAo.Areas.Admin.Models.WebsiteQuanAo.Areas.Admin.Models;
using WebsiteQuanAo.Models;
namespace WebsiteQuanAo.Areas.Admin.Controllers
{
    public class HomeController : Controller
    {
        DataQuanAoDataContext db = new DataQuanAoDataContext();
        // GET: Admin/Home
        public ActionResult Index()
        {
            var revenueByMonth = db.DonHangs
          .Where(dh => dh.NgayGiao != null && dh.NgayGiao.Value.Year == DateTime.Now.Year)
          .GroupBy(dh => new { Year = dh.NgayGiao.Value.Year, Month = dh.NgayGiao.Value.Month })
         .Select(group => new RevenueViewModel
         {
             Year = group.Key.Year,
             Month = group.Key.Month,
             Revenue = group.Sum(dh => dh.TongTien ?? 0) // Coalesce to 0 if TongTien is null
         })
          .ToList();


            ViewBag.RevenueByMonth = revenueByMonth;

            return View();
        }
        [HttpGet]
        public ActionResult LoginAd()
        {
            return View();
        }
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult LoginAd(string username, string password)
        {
            if (ModelState.IsValid)
            {
                var user = db.Admins.Where(s => s.Username.Equals(username) && s.Password.Equals(password)).ToList();
                if (user.Count() > 0)
                {
                    if (user != null)
                    {
                        TempData["SuccessMessage"] = "Chào mừng bạn đăng nhập.";
                        Session["Username"] = user.FirstOrDefault().Username;
                        return RedirectToAction("Index", "Home");
                    }
                    else
                    {
                        ViewBag.Error = "Tên đăng nhập hoặc mật khẩu không đúng.";
                        return RedirectToAction("LoginAd");
                    }
                }
            }
            return View();
        }
        [HttpGet]
        public ActionResult ChangePass()
        {
            return View();
        }
        [HttpPost]
        [ValidateInput(false)]
        public ActionResult ChangePass(FormCollection f)
        {
            var user = (from s in db.Admins where s.Username == Session["Username"].ToString() select s).SingleOrDefault();

            if (ModelState.IsValid)
            {
                if (f["NewPass"] == f["CormNewPass"] && f["CurPass"] == user.Password)
                {
                    user.Password = f["NewPass"];
                }

                db.SubmitChanges();
                TempData["SuccessMessage"] = "Mật khẩu đã được cập nhật thành công.";
                return RedirectToAction("Details");
            }
            return View(user);
        }
        public ActionResult Details()
        {
            var user = (from s in db.Admins where s.Username == Session["Username"].ToString() select s).SingleOrDefault(); // Lấy một đối tượng Admin
            return View(user);
        }

        public ActionResult LogOut()
        {
            Session.Remove("Username");
            return RedirectToAction("Index", "Home");
        }
    }
}