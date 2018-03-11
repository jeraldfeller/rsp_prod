<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Frequently Asked Questions</title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<meta name="keywords" content="Frequently Asked Questions" />
<meta name="description" content="" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {
	color: #FFFFFF;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.style2 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.loginString {
	color: #FF0000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: normal;
}
.style4 {
	font-size: 17px;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {color: #0099FF}
.style6 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.txtbox1 {
	font: 11px Arial, Helvetica, sans-serif;
	color: #000000;
	height: 15px;
	width: 152px;
	border: 1px solid #0F6592;
	vertical-align: bottom;
}
.listbox {
	height: 15px;
	width: 132px;
	border: 1px solid #0F6592;
}
.txtbox2 {
	font: 11px Arial, Helvetica, sans-serif;
	color: #000000;
	height: 15px;
	width: 132px;
	border: 1px solid #0F6592;
}
.txtbox3 {
	font: 11px Arial, Helvetica, sans-serif;
	color: #000000;
	height: 105px;
	width: 350px;
	border: 1px solid #0F6592;
}
.contentbody {
	border: 1px solid #5DA7E1;
	background-color: #FAFCFE;
}
.style9 {
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
	color: #0B4A6C
}
-->
</style>
<SCRIPT language="JavaScript" type="text/javascript">
var DefaultDateFormat = 'MM/DD/YYYY'; // If no date format is supplied, this will be used instead
var HideWait = 3; // Number of seconds before the calendar will disappear
var Y2kPivotPoint = 76; // 2-digit years before this point will be created in the 21st century
var UnselectedMonthText = ''; // Text to display in the 1st month list item when the date isn't required
var FontSize = 11; // In pixels
var FontFamily = 'Tahoma';
var CellWidth = 18;
var CellHeight = 16;
var ImageURL = 'calendar.jpg';
var NextURL = 'next.gif';
var PrevURL = 'prev.gif';
var CalBGColor = 'white';
var TopRowBGColor = 'buttonface';
var DayBGColor = 'lightgrey';

// Global variables
var ZCounter = 100;
var Today = new Date();
var WeekDays = new Array('S','M','T','W','T','F','S');
var MonthDays = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var MonthNames = new Array('January','February','March','April','May','June','July','August','September','October','November','December');

// Write out the stylesheet definition for the calendar
with (document) {
   writeln('<style>');
   writeln('td.calendarDateInput {letter-spacing:normal;line-height:normal;font-family:' + FontFamily + ',Sans-Serif;font-size:' + FontSize + 'px;}');
   writeln('select.calendarDateInput {letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;}');
   writeln('input.calendarDateInput {letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;}');
   writeln('</style>');
}

// Only allows certain keys to be used in the date field
function YearDigitsOnly(e) {
   var KeyCode = (e.keyCode) ? e.keyCode : e.which;
   return ((KeyCode == 8) // backspace
        || (KeyCode == 9) // tab
        || (KeyCode == 37) // left arrow
        || (KeyCode == 39) // right arrow
        || (KeyCode == 46) // delete
        || ((KeyCode > 47) && (KeyCode < 58)) // 0 - 9
   );
}

// Gets the absolute pixel position of the supplied element
function GetTagPixels(StartTag, Direction) {
   var PixelAmt = (Direction == 'LEFT') ? StartTag.offsetLeft : StartTag.offsetTop;
   while ((StartTag.tagName != 'BODY') && (StartTag.tagName != 'HTML')) {
      StartTag = StartTag.offsetParent;
      PixelAmt += (Direction == 'LEFT') ? StartTag.offsetLeft : StartTag.offsetTop;
   }
   return PixelAmt;
}

// Is the specified select-list behind the calendar?
function BehindCal(SelectList, CalLeftX, CalRightX, CalTopY, CalBottomY, ListTopY) {
   var ListLeftX = GetTagPixels(SelectList, 'LEFT');
   var ListRightX = ListLeftX + SelectList.offsetWidth;
   var ListBottomY = ListTopY + SelectList.offsetHeight;
   return (((ListTopY < CalBottomY) && (ListBottomY > CalTopY)) && ((ListLeftX < CalRightX) && (ListRightX > CalLeftX)));
}

// For IE, hides any select-lists that are behind the calendar
function FixSelectLists(Over) {
   if (navigator.appName == 'Microsoft Internet Explorer') {
      var CalDiv = this.getCalendar();
      var CalLeftX = CalDiv.offsetLeft;
      var CalRightX = CalLeftX + CalDiv.offsetWidth;
      var CalTopY = CalDiv.offsetTop;
      var CalBottomY = CalTopY + (CellHeight * 9);
      var FoundCalInput = false;
      formLoop :
      for (var j=this.formNumber;j<document.forms.length;j++) {
         for (var i=0;i<document.forms[j].elements.length;i++) {
            if (typeof document.forms[j].elements[i].type == 'string') {
               if ((document.forms[j].elements[i].type == 'hidden') && (document.forms[j].elements[i].name == this.hiddenFieldName)) {
                  FoundCalInput = true;
                  i += 3; // 3 elements between the 1st hidden field and the last year input field
               }
               if (FoundCalInput) {
                  if (document.forms[j].elements[i].type.substr(0,6) == 'select') {
                     ListTopY = GetTagPixels(document.forms[j].elements[i], 'TOP');
                     if (ListTopY < CalBottomY) {
                        if (BehindCal(document.forms[j].elements[i], CalLeftX, CalRightX, CalTopY, CalBottomY, ListTopY)) {
                           document.forms[j].elements[i].style.visibility = (Over) ? 'hidden' : 'visible';
                        }
                     }
                     else break formLoop;
                  }
               }
            }
         }
      }
   }
}

// Displays a message in the status bar when hovering over the calendar days
function DayCellHover(Cell, Over, Color, HoveredDay) {
   Cell.style.backgroundColor = (Over) ? DayBGColor : Color;
   if (Over) {
      if ((this.yearValue == Today.getFullYear()) && (this.monthIndex == Today.getMonth()) && (HoveredDay == Today.getDate())) self.status = 'Click to select today';
      else {
         var Suffix = HoveredDay.toString();
         switch (Suffix.substr(Suffix.length - 1, 1)) {
            case '1' : Suffix += (HoveredDay == 11) ? 'th' : 'st'; break;
            case '2' : Suffix += (HoveredDay == 12) ? 'th' : 'nd'; break;
            case '3' : Suffix += (HoveredDay == 13) ? 'th' : 'rd'; break;
            default : Suffix += 'th'; break;
         }
         self.status = 'Click to select ' + this.monthName + ' ' + Suffix;
      }
   }
   else self.status = '';
   return true;
}

// Sets the form elements after a day has been picked from the calendar
function PickDisplayDay(ClickedDay) {
   this.show();
   var MonthList = this.getMonthList();
   var DayList = this.getDayList();
   var YearField = this.getYearField();
   FixDayList(DayList, GetDayCount(this.displayed.yearValue, this.displayed.monthIndex));
   // Select the month and day in the lists
   for (var i=0;i<MonthList.length;i++) {
      if (MonthList.options[i].value == this.displayed.monthIndex) MonthList.options[i].selected = true;
   }
   for (var j=1;j<=DayList.length;j++) {
      if (j == ClickedDay) DayList.options[j-1].selected = true;
   }
   this.setPicked(this.displayed.yearValue, this.displayed.monthIndex, ClickedDay);
   // Change the year, if necessary
   YearField.value = this.picked.yearPad;
   YearField.defaultValue = YearField.value;
}

// Builds the HTML for the calendar days
function BuildCalendarDays() {
   var Rows = 5;
   if (((this.displayed.dayCount == 31) && (this.displayed.firstDay > 4)) || ((this.displayed.dayCount == 30) && (this.displayed.firstDay == 6))) Rows = 6;
   else if ((this.displayed.dayCount == 28) && (this.displayed.firstDay == 0)) Rows = 4;
   var HTML = '<table width="' + (CellWidth * 7) + '" cellspacing="0" cellpadding="1" style="cursor:default">';
   for (var j=0;j<Rows;j++) {
      HTML += '<tr>';
      for (var i=1;i<=7;i++) {
         Day = (j * 7) + (i - this.displayed.firstDay);
         if ((Day >= 1) && (Day <= this.displayed.dayCount)) {
            if ((this.displayed.yearValue == this.picked.yearValue) && (this.displayed.monthIndex == this.picked.monthIndex) && (Day == this.picked.day)) {
               TextStyle = 'color:white;font-weight:bold;'
               BackColor = DayBGColor;
            }
            else {
               TextStyle = 'color:black;'
               BackColor = CalBGColor;
            }
            if ((this.displayed.yearValue == Today.getFullYear()) && (this.displayed.monthIndex == Today.getMonth()) && (Day == Today.getDate())) TextStyle += 'border:1px solid darkred;padding:0px;';
            HTML += '<td align="center" class="calendarDateInput" style="cursor:default;height:' + CellHeight + ';width:' + CellWidth + ';' + TextStyle + ';background-color:' + BackColor + '" onClick="' + this.objName + '.pickDay(' + Day + ')" onMouseOver="return ' + this.objName + '.displayed.dayHover(this,true,\'' + BackColor + '\',' + Day + ')" onMouseOut="return ' + this.objName + '.displayed.dayHover(this,false,\'' + BackColor + '\')">' + Day + '</td>';
         }
         else HTML += '<td class="calendarDateInput" style="height:' + CellHeight + '">&nbsp;</td>';
      }
      HTML += '</tr>';
   }
   return HTML += '</table>';
}

// Determines which century to use (20th or 21st) when dealing with 2-digit years
function GetGoodYear(YearDigits) {
   if (YearDigits.length == 4) return YearDigits;
   else {
      var Millennium = (YearDigits < Y2kPivotPoint) ? 2000 : 1900;
      return Millennium + parseInt(YearDigits,10);
   }
}

// Returns the number of days in a month (handles leap-years)
function GetDayCount(SomeYear, SomeMonth) {
   return ((SomeMonth == 1) && ((SomeYear % 400 == 0) || ((SomeYear % 4 == 0) && (SomeYear % 100 != 0)))) ? 29 : MonthDays[SomeMonth];
}

// Highlights the buttons
function VirtualButton(Cell, ButtonDown) {
   if (ButtonDown) {
      Cell.style.borderLeft = 'buttonshadow 1px solid';
      Cell.style.borderTop = 'buttonshadow 1px solid';
      Cell.style.borderBottom = 'buttonhighlight 1px solid';
      Cell.style.borderRight = 'buttonhighlight 1px solid';
   }
   else {
      Cell.style.borderLeft = 'buttonhighlight 1px solid';
      Cell.style.borderTop = 'buttonhighlight 1px solid';
      Cell.style.borderBottom = 'buttonshadow 1px solid';
      Cell.style.borderRight = 'buttonshadow 1px solid';
   }
}

// Mouse-over for the previous/next month buttons
function NeighborHover(Cell, Over, DateObj) {
   if (Over) {
      VirtualButton(Cell, false);
      self.status = 'Click to view ' + DateObj.fullName;
   }
   else {
      Cell.style.border = 'buttonface 1px solid';
      self.status = '';
   }
   return true;
}

// Adds/removes days from the day list, depending on the month/year
function FixDayList(DayList, NewDays) {
   var DayPick = DayList.selectedIndex + 1;
   if (NewDays != DayList.length) {
      var OldSize = DayList.length;
      for (var k=Math.min(NewDays,OldSize);k<Math.max(NewDays,OldSize);k++) {
         (k >= NewDays) ? DayList.options[NewDays] = null : DayList.options[k] = new Option(k+1, k+1);
      }
      DayPick = Math.min(DayPick, NewDays);
      DayList.options[DayPick-1].selected = true;
   }
   return DayPick;
}

// Resets the year to its previous valid value when something invalid is entered
function FixYearInput(YearField) {
   var YearRE = new RegExp('\\d{' + YearField.defaultValue.length + '}');
   if (!YearRE.test(YearField.value)) YearField.value = YearField.defaultValue;
}

// Displays a message in the status bar when hovering over the calendar icon
function CalIconHover(Over) {
   var Message = (this.isShowing()) ? 'hide' : 'show';
   self.status = (Over) ? 'Click to ' + Message + ' the calendar' : '';
   return true;
}

// Starts the timer over from scratch
function CalTimerReset() {
   eval('clearTimeout(' + this.timerID + ')');
   eval(this.timerID + '=setTimeout(\'' + this.objName + '.show()\',' + (HideWait * 1000) + ')');
}

// The timer for the calendar
function DoTimer(CancelTimer) {
   if (CancelTimer) eval('clearTimeout(' + this.timerID + ')');
   else {
      eval(this.timerID + '=null');
      this.resetTimer();
   }
}

// Show or hide the calendar
function ShowCalendar() {
   if (this.isShowing()) {
      var StopTimer = true;
      this.getCalendar().style.zIndex = --ZCounter;
      this.getCalendar().style.visibility = 'hidden';
      this.fixSelects(false);
   }
   else {
      var StopTimer = false;
      this.fixSelects(true);
      this.getCalendar().style.zIndex = ++ZCounter;
      this.getCalendar().style.visibility = 'visible';
   }
   this.handleTimer(StopTimer);
   self.status = '';
}

// Hides the input elements when the "blank" month is selected
function SetElementStatus(Hide) {
   this.getDayList().style.visibility = (Hide) ? 'hidden' : 'visible';
   this.getYearField().style.visibility = (Hide) ? 'hidden' : 'visible';
   this.getCalendarLink().style.visibility = (Hide) ? 'hidden' : 'visible';
}

// Sets the date, based on the month selected
function CheckMonthChange(MonthList) {
   var DayList = this.getDayList();
   if (MonthList.options[MonthList.selectedIndex].value == '') {
      DayList.selectedIndex = 0;
      this.hideElements(true);
      this.setHidden('');
   }
   else {
      this.hideElements(false);
      if (this.isShowing()) {
         this.resetTimer(); // Gives the user more time to view the calendar with the newly-selected month
         this.getCalendar().style.zIndex = ++ZCounter; // Make sure this calendar is on top of any other calendars
      }
      var DayPick = FixDayList(DayList, GetDayCount(this.picked.yearValue, MonthList.options[MonthList.selectedIndex].value));
      this.setPicked(this.picked.yearValue, MonthList.options[MonthList.selectedIndex].value, DayPick);
   }
}

// Sets the date, based on the day selected
function CheckDayChange(DayList) {
   if (this.isShowing()) this.show();
   this.setPicked(this.picked.yearValue, this.picked.monthIndex, DayList.selectedIndex+1);
}

// Changes the date when a valid year has been entered
function CheckYearInput(YearField) {
   if ((YearField.value.length == YearField.defaultValue.length) && (YearField.defaultValue != YearField.value)) {
      if (this.isShowing()) {
         this.resetTimer(); // Gives the user more time to view the calendar with the newly-entered year
         this.getCalendar().style.zIndex = ++ZCounter; // Make sure this calendar is on top of any other calendars
      }
      var NewYear = GetGoodYear(YearField.value);
      var MonthList = this.getMonthList();
      var NewDay = FixDayList(this.getDayList(), GetDayCount(NewYear, this.picked.monthIndex));
      this.setPicked(NewYear, this.picked.monthIndex, NewDay);
      YearField.defaultValue = YearField.value;
   }
}

// Holds characteristics about a date
function dateObject() {
   if (Function.call) { // Used when 'call' method of the Function object is supported
      var ParentObject = this;
      var ArgumentStart = 0;
   }
   else { // Used with 'call' method of the Function object is NOT supported
      var ParentObject = arguments[0];
      var ArgumentStart = 1;
   }
   ParentObject.date = (arguments.length == (ArgumentStart+1)) ? new Date(arguments[ArgumentStart+0]) : new Date(arguments[ArgumentStart+0], arguments[ArgumentStart+1], arguments[ArgumentStart+2]);
   ParentObject.yearValue = ParentObject.date.getFullYear();
   ParentObject.monthIndex = ParentObject.date.getMonth();
   ParentObject.monthName = MonthNames[ParentObject.monthIndex];
   ParentObject.fullName = ParentObject.monthName + ' ' + ParentObject.yearValue;
   ParentObject.day = ParentObject.date.getDate();
   ParentObject.dayCount = GetDayCount(ParentObject.yearValue, ParentObject.monthIndex);
   var FirstDate = new Date(ParentObject.yearValue, ParentObject.monthIndex, 1);
   ParentObject.firstDay = FirstDate.getDay();
}

// Keeps track of the date that goes into the hidden field
function storedMonthObject(DateFormat, DateYear, DateMonth, DateDay) {
   (Function.call) ? dateObject.call(this, DateYear, DateMonth, DateDay) : dateObject(this, DateYear, DateMonth, DateDay);
   this.yearPad = this.yearValue.toString();
   this.monthPad = (this.monthIndex < 9) ? '0' + String(this.monthIndex + 1) : this.monthIndex + 1;
   this.dayPad = (this.day < 10) ? '0' + this.day.toString() : this.day;
   this.monthShort = this.monthName.substr(0,3).toUpperCase();
   // Formats the year with 2 digits instead of 4
   if (DateFormat.indexOf('YYYY') == -1) this.yearPad = this.yearPad.substr(2);
   // Define the date-part delimiter
   if (DateFormat.indexOf('/') >= 0) var Delimiter = '/';
   else if (DateFormat.indexOf('-') >= 0) var Delimiter = '-';
   else var Delimiter = '';
   // Determine the order of the months and days
   if (/DD?.?((MON)|(MM?M?))/.test(DateFormat)) {
      this.formatted = this.dayPad + Delimiter;
      this.formatted += (RegExp.$1.length == 3) ? this.monthShort : this.monthPad;
   }
   else if (/((MON)|(MM?M?))?.?DD?/.test(DateFormat)) {
      this.formatted = (RegExp.$1.length == 3) ? this.monthShort : this.monthPad;
      this.formatted += Delimiter + this.dayPad;
   }
   // Either prepend or append the year to the formatted date
   this.formatted = (DateFormat.substr(0,2) == 'YY') ? this.yearPad + Delimiter + this.formatted : this.formatted + Delimiter + this.yearPad;
}

// Object for the current displayed month
function displayMonthObject(ParentObject, DateYear, DateMonth, DateDay) {
   (Function.call) ? dateObject.call(this, DateYear, DateMonth, DateDay) : dateObject(this, DateYear, DateMonth, DateDay);
   this.displayID = ParentObject.hiddenFieldName + '_Current_ID';
   this.getDisplay = new Function('return document.getElementById(this.displayID)');
   this.dayHover = DayCellHover;
   this.goCurrent = new Function(ParentObject.objName + '.getCalendar().style.zIndex=++ZCounter;' + ParentObject.objName + '.setDisplayed(Today.getFullYear(),Today.getMonth());');
   if (ParentObject.formNumber >= 0) this.getDisplay().innerHTML = this.fullName;
}

// Object for the previous/next buttons
function neighborMonthObject(ParentObject, IDText, DateMS) {
   (Function.call) ? dateObject.call(this, DateMS) : dateObject(this, DateMS);
   this.buttonID = ParentObject.hiddenFieldName + '_' + IDText + '_ID';
   this.hover = new Function('C','O','NeighborHover(C,O,this)');
   this.getButton = new Function('return document.getElementById(this.buttonID)');
   this.go = new Function(ParentObject.objName + '.getCalendar().style.zIndex=++ZCounter;' + ParentObject.objName + '.setDisplayed(this.yearValue,this.monthIndex);');
   if (ParentObject.formNumber >= 0) this.getButton().title = this.monthName;
}

// Sets the currently-displayed month object
function SetDisplayedMonth(DispYear, DispMonth) {
   this.displayed = new displayMonthObject(this, DispYear, DispMonth, 1);
   // Creates the previous and next month objects
   this.previous = new neighborMonthObject(this, 'Previous', this.displayed.date.getTime() - 86400000);
   this.next = new neighborMonthObject(this, 'Next', this.displayed.date.getTime() + (86400000 * (this.displayed.dayCount + 1)));
   // Creates the HTML for the calendar
   if (this.formNumber >= 0) this.getDayTable().innerHTML = this.buildCalendar();
}

// Sets the current selected date
function SetPickedMonth(PickedYear, PickedMonth, PickedDay) {
   this.picked = new storedMonthObject(this.format, PickedYear, PickedMonth, PickedDay);
   this.setHidden(this.picked.formatted);
   this.setDisplayed(PickedYear, PickedMonth);
}

// The calendar object
function calendarObject(DateName, DateFormat, DefaultDate) {

   /* Properties */
   this.hiddenFieldName = DateName;
   this.monthListID = DateName + '_Month_ID';
   this.dayListID = DateName + '_Day_ID';
   this.yearFieldID = DateName + '_Year_ID';
   this.monthDisplayID = DateName + '_Current_ID';
   this.calendarID = DateName + '_ID';
   this.dayTableID = DateName + '_DayTable_ID';
   this.calendarLinkID = this.calendarID + '_Link';
   this.timerID = this.calendarID + '_Timer';
   this.objName = DateName + '_Object';
   this.format = DateFormat;
   this.formNumber = -1;
   this.picked = null;
   this.displayed = null;
   this.previous = null;
   this.next = null;

   /* Methods */
   this.setPicked = SetPickedMonth;
   this.setDisplayed = SetDisplayedMonth;
   this.checkYear = CheckYearInput;
   this.fixYear = FixYearInput;
   this.changeMonth = CheckMonthChange;
   this.changeDay = CheckDayChange;
   this.resetTimer = CalTimerReset;
   this.hideElements = SetElementStatus;
   this.show = ShowCalendar;
   this.handleTimer = DoTimer;
   this.iconHover = CalIconHover;
   this.buildCalendar = BuildCalendarDays;
   this.pickDay = PickDisplayDay;
   this.fixSelects = FixSelectLists;
   this.setHidden = new Function('D','if (this.formNumber >= 0) this.getHiddenField().value=D');
   // Returns a reference to these elements
   this.getHiddenField = new Function('return document.forms[this.formNumber].elements[this.hiddenFieldName]');
   this.getMonthList = new Function('return document.getElementById(this.monthListID)');
   this.getDayList = new Function('return document.getElementById(this.dayListID)');
   this.getYearField = new Function('return document.getElementById(this.yearFieldID)');
   this.getCalendar = new Function('return document.getElementById(this.calendarID)');
   this.getDayTable = new Function('return document.getElementById(this.dayTableID)');
   this.getCalendarLink = new Function('return document.getElementById(this.calendarLinkID)');
   this.getMonthDisplay = new Function('return document.getElementById(this.monthDisplayID)');
   this.isShowing = new Function('return !(this.getCalendar().style.visibility != \'visible\')');

   /* Constructor */
   // Functions used only by the constructor
   function getMonthIndex(MonthAbbr) { // Returns the index (0-11) of the supplied month abbreviation
      for (var MonPos=0;MonPos<MonthNames.length;MonPos++) {
         if (MonthNames[MonPos].substr(0,3).toUpperCase() == MonthAbbr.toUpperCase()) break;
      }
      return MonPos;
   }
   function SetGoodDate(CalObj, Notify) { // Notifies the user about their bad default date, and sets the current system date
      CalObj.setPicked(Today.getFullYear(), Today.getMonth(), Today.getDate());
      if (Notify) alert('WARNING: The supplied date is not in valid \'' + DateFormat + '\' format: ' + DefaultDate + '.\nTherefore, the current system date will be used instead: ' + CalObj.picked.formatted);
   }
   // Main part of the constructor
   if (DefaultDate != '') {
      if ((this.format == 'YYYYMMDD') && (/^(\d{4})(\d{2})(\d{2})$/.test(DefaultDate))) this.setPicked(RegExp.$1, parseInt(RegExp.$2,10)-1, RegExp.$3);
      else {
         // Get the year
         if ((this.format.substr(0,2) == 'YY') && (/^(\d{2,4})(-|\/)/.test(DefaultDate))) { // Year is at the beginning
            var YearPart = GetGoodYear(RegExp.$1);
            // Determine the order of the months and days
            if (/(-|\/)(\w{1,3})(-|\/)(\w{1,3})$/.test(DefaultDate)) {
               var MidPart = RegExp.$2;
               var EndPart = RegExp.$4;
               if (/D$/.test(this.format)) { // Ends with days
                  var DayPart = EndPart;
                  var MonthPart = MidPart;
               }
               else {
                  var DayPart = MidPart;
                  var MonthPart = EndPart;
               }
               MonthPart = (/\d{1,2}/i.test(MonthPart)) ? parseInt(MonthPart,10)-1 : getMonthIndex(MonthPart);
               this.setPicked(YearPart, MonthPart, DayPart);
            }
            else SetGoodDate(this, true);
         }
         else if (/(-|\/)(\d{2,4})$/.test(DefaultDate)) { // Year is at the end
            var YearPart = GetGoodYear(RegExp.$2);
            // Determine the order of the months and days
            if (/^(\w{1,3})(-|\/)(\w{1,3})(-|\/)/.test(DefaultDate)) {
               if (this.format.substr(0,1) == 'D') { // Starts with days
                  var DayPart = RegExp.$1;
                  var MonthPart = RegExp.$3;
               }
               else { // Starts with months
                  var MonthPart = RegExp.$1;
                  var DayPart = RegExp.$3;
               }
               MonthPart = (/\d{1,2}/i.test(MonthPart)) ? parseInt(MonthPart,10)-1 : getMonthIndex(MonthPart);
               this.setPicked(YearPart, MonthPart, DayPart);
            }
            else SetGoodDate(this, true);
         }
         else SetGoodDate(this, true);
      }
   }
}

// Main function that creates the form elements
function DateInput(DateName, Required, DateFormat, DefaultDate) {
   if (arguments.length == 0) document.writeln('<span style="color:red;font-size:' + FontSize + 'px;font-family:' + FontFamily + ';">ERROR: Missing required parameter in call to \'DateInput\': [name of hidden date field].</span>');
   else {
      // Handle DateFormat
      if (arguments.length < 3) { // The format wasn't passed in, so use default
         DateFormat = DefaultDateFormat;
         if (arguments.length < 2) Required = false;
      }
      else if (/^(Y{2,4}(-|\/)?)?((MON)|(MM?M?)|(DD?))(-|\/)?((MON)|(MM?M?)|(DD?))((-|\/)Y{2,4})?$/i.test(DateFormat)) DateFormat = DateFormat.toUpperCase();
      else { // Passed-in DateFormat was invalid, use default format instead
         var AlertMessage = 'WARNING: The supplied date format for the \'' + DateName + '\' field is not valid: ' + DateFormat + '\nTherefore, the default date format will be used instead: ' + DefaultDateFormat;
         DateFormat = DefaultDateFormat;
         if (arguments.length == 4) { // DefaultDate was passed in with an invalid date format
            var CurrentDate = new storedMonthObject(DateFormat, Today.getFullYear(), Today.getMonth(), Today.getDate());
            AlertMessage += '\n\nThe supplied date (' + DefaultDate + ') cannot be interpreted with the invalid format.\nTherefore, the current system date will be used instead: ' + CurrentDate.formatted;
            DefaultDate = CurrentDate.formatted;
         }
         alert(AlertMessage);
      }
      // Define the current date if it wasn't set already
      if (!CurrentDate) var CurrentDate = new storedMonthObject(DateFormat, Today.getFullYear(), Today.getMonth(), Today.getDate());
      // Handle DefaultDate
      if (arguments.length < 4) { // The date wasn't passed in
         DefaultDate = (Required) ? CurrentDate.formatted : ''; // If required, use today's date
      }
      // Creates the calendar object!
      eval(DateName + '_Object=new calendarObject(\'' + DateName + '\',\'' + DateFormat + '\',\'' + DefaultDate + '\')');
      // Determine initial viewable state of day, year, and calendar icon
      if ((Required) || (arguments.length == 4)) {
         var InitialStatus = '';
         var InitialDate = eval(DateName + '_Object.picked.formatted');
      }
      else {
         var InitialStatus = ' style="visibility:hidden"';
         var InitialDate = '';
         eval(DateName + '_Object.setPicked(' + Today.getFullYear() + ',' + Today.getMonth() + ',' + Today.getDate() + ')');
      }
      // Create the form elements
      with (document) {
         writeln('<input type="hidden" name="' + DateName + '" value="' + InitialDate + '">');
         // Find this form number
         for (var f=0;f<forms.length;f++) {
            for (var e=0;e<forms[f].elements.length;e++) {
               if (typeof forms[f].elements[e].type == 'string') {
                  if ((forms[f].elements[e].type == 'hidden') && (forms[f].elements[e].name == DateName)) {
                     eval(DateName + '_Object.formNumber='+f);
                     break;
                  }
               }
            }
         }
         writeln('<table cellpadding="0" cellspacing="2"><tr>' + String.fromCharCode(13) + '<td valign="middle">');
         writeln('<select class="calendarDateInput" id="' + DateName + '_Month_ID" onChange="' + DateName + '_Object.changeMonth(this)">');
         if (!Required) {
            var NoneSelected = (DefaultDate == '') ? ' selected' : '';
            writeln('<option value=""' + NoneSelected + '>' + UnselectedMonthText + '</option>');
         }
         for (var i=0;i<12;i++) {
            MonthSelected = ((DefaultDate != '') && (eval(DateName + '_Object.picked.monthIndex') == i)) ? ' selected' : '';
            writeln('<option value="' + i + '"' + MonthSelected + '>' + MonthNames[i].substr(0,3) + '</option>');
         }
         writeln('</select>' + String.fromCharCode(13) + '</td>' + String.fromCharCode(13) + '<td valign="middle">');
         writeln('<select' + InitialStatus + ' class="calendarDateInput" id="' + DateName + '_Day_ID" onChange="' + DateName + '_Object.changeDay(this)">');
         for (var j=1;j<=eval(DateName + '_Object.picked.dayCount');j++) {
            DaySelected = ((DefaultDate != '') && (eval(DateName + '_Object.picked.day') == j)) ? ' selected' : '';
            writeln('<option' + DaySelected + '>' + j + '</option>');
         }
         writeln('</select>' + String.fromCharCode(13) + '</td>' + String.fromCharCode(13) + '<td valign="middle">');
         writeln('<input' + InitialStatus + ' class="calendarDateInput" type="text" id="' + DateName + '_Year_ID" size="' + eval(DateName + '_Object.picked.yearPad.length') + '" maxlength="' + eval(DateName + '_Object.picked.yearPad.length') + '" title="Year" value="' + eval(DateName + '_Object.picked.yearPad') + '" onKeyPress="return YearDigitsOnly(window.event)" onKeyUp="' + DateName + '_Object.checkYear(this)" onBlur="' + DateName + '_Object.fixYear(this)">');
         write('<td valign="middle">' + String.fromCharCode(13) + '<a' + InitialStatus + ' id="' + DateName + '_ID_Link" href="javascript:' + DateName + '_Object.show()" onMouseOver="return ' + DateName + '_Object.iconHover(true)" onMouseOut="return ' + DateName + '_Object.iconHover(false)"><img src="' + ImageURL + '" align="baseline" title="Calendar" border="0"></a>&nbsp;');
         writeln('<span id="' + DateName + '_ID" style="position:absolute;visibility:hidden;width:' + (CellWidth * 7) + 'px;background-color:' + CalBGColor + ';border:1px solid dimgray;" onMouseOver="' + DateName + '_Object.handleTimer(true)" onMouseOut="' + DateName + '_Object.handleTimer(false)">');
         writeln('<table width="' + (CellWidth * 7) + '" cellspacing="0" cellpadding="1">' + String.fromCharCode(13) + '<tr style="background-color:' + TopRowBGColor + ';">');
         writeln('<td id="' + DateName + '_Previous_ID" style="cursor:default" align="center" class="calendarDateInput" style="height:' + CellHeight + '" onClick="' + DateName + '_Object.previous.go()" onMouseDown="VirtualButton(this,true)" onMouseUp="VirtualButton(this,false)" onMouseOver="return ' + DateName + '_Object.previous.hover(this,true)" onMouseOut="return ' + DateName + '_Object.previous.hover(this,false)" title="' + eval(DateName + '_Object.previous.monthName') + '"><img src="' + PrevURL + '"></td>');
         writeln('<td id="' + DateName + '_Current_ID" style="cursor:pointer" align="center" class="calendarDateInput" style="height:' + CellHeight + '" colspan="5" onClick="' + DateName + '_Object.displayed.goCurrent()" onMouseOver="self.status=\'Click to view ' + CurrentDate.fullName + '\';return true;" onMouseOut="self.status=\'\';return true;" title="Show Current Month">' + eval(DateName + '_Object.displayed.fullName') + '</td>');
         writeln('<td id="' + DateName + '_Next_ID" style="cursor:default" align="center" class="calendarDateInput" style="height:' + CellHeight + '" onClick="' + DateName + '_Object.next.go()" onMouseDown="VirtualButton(this,true)" onMouseUp="VirtualButton(this,false)" onMouseOver="return ' + DateName + '_Object.next.hover(this,true)" onMouseOut="return ' + DateName + '_Object.next.hover(this,false)" title="' + eval(DateName + '_Object.next.monthName') + '"><img src="' + NextURL + '"></td></tr>' + String.fromCharCode(13) + '<tr>');
         for (var w=0;w<7;w++) writeln('<td width="' + CellWidth + '" align="center" class="calendarDateInput" style="height:' + CellHeight + ';width:' + CellWidth + ';font-weight:bold;border-top:1px solid dimgray;border-bottom:1px solid dimgray;">' + WeekDays[w] + '</td>');
         writeln('</tr>' + String.fromCharCode(13) + '</table>' + String.fromCharCode(13) + '<span id="' + DateName + '_DayTable_ID">' + eval(DateName + '_Object.buildCalendar()') + '</span>' + String.fromCharCode(13) + '</span>' + String.fromCharCode(13) + '</td>' + String.fromCharCode(13) + '</tr>' + String.fromCharCode(13) + '</table>');
      }
   }
}
</script>

</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="13"><img name="head_r1_c1" src="images/head_r1_c1.gif" width="13" height="27" border="0" id="head_r1_c1" alt="" /></td>
        <td width="50%" background="images/head_r1_c2.gif"><div align="left" class="style1">www.realtysignpost.com</div></td>
        <td width="50%" background="images/head_r1_c2.gif"><div align="right" class="style1"><a class="helpLink" href="help_system.php?page_url=faq.php" alt="View Help Topics for this Page">Help</a></div></td>
        <td width="20"><a href="help_system.php?page_url=faq.php" alt="View Help Topics for this Page"><img name="head_que" src="images/head_que.gif" width="20" height="27" border="0" id="head_que" alt="" /></a></td>

        <td width="14"><img name="head_r1_c5" src="images/head_r1_c5.gif" width="14" height="27" border="0" id="head_r1_c5" alt="" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="13"><img name="head_r2_c1" src="images/head_r2_c1.gif" width="13" height="98" border="0" id="head_r2_c1" alt="" /></td>
        <td valign="top" background="images/head_r2_c3.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">

          <tr>
            <td width="310"><img name="head_r2_c2" src="images/head_r2_c2.jpg" width="310" height="98" border="0" id="head_r2_c2" alt="" /></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="94%"><div align="right"><span class="style2"><a class="headerMenu" href="index.php">Home</a> | <a class="headerMenu" href="about_us.php">About Us</a> | <a class="headerMenu" href="faq.php">FAQ</a> | <a class="headerMenu" href="terms_of_service.php">Terms of Service</a> | <a class="headerMenu" href="account_overview.php">My Account</a> | <a class="headerMenu" href="service_plans.php">Service Plans</a> | <a class="headerMenu" href="contact_us.php">Contact Us</a> | <a class="headerMenu" href="index.php?action=logout">Logoff</a></span></div></td>

                <td width="3%">&nbsp;</td>
              </tr>
			  <tr>
                <td width="3%">&nbsp;</td>
                <td width="94%"><div align="right"><span class="loginString"></span></div></td>
                <td width="3%">&nbsp;</td>
              </tr>
            </table></td>
          </tr>

        </table></td>
        <td width="14"><img name="head_r2_c5" src="images/head_r2_c5.gif" width="14" height="98" border="0" id="head_r2_c5" alt="" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="11" background="images/body_r1_c1.jpg"></td>

        <td align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="263" valign="top"><table width="262" border="0" cellspacing="0" cellpadding="0">
              	<tr>
					<td width="262" align="center">
						<table width="260" cellspacing="0" cellpaddig="0">
							<tr>
                  <td><table width="260" border="0" cellspacing="0" cellpadding="0">
                    <tr>

                      <td><img name="body_r1_c9" src="images/my_orders_heading.jpg" width="260" height="48" border="0" alt="" /></td>
                    </tr>
                    <tr>
                      <td height="109" width="100%" align="center" valign="top" class="columnBox">
					  	<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td height="8" width="100%"><img src="images.pixel_trans.gif" height="8" width="260"></td>
							</tr>
															<tr>

									<td class="style6" width="100%" align="left" height="19">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
												<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
												<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
												<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="order_create.php?order_type=1">Request Sign Post Install</a></td>
											</tr>

										</table>
									</td>
								</tr>
								<tr>
									<td height="4"><img src="images.pixel_trans.gif" height="4" width="1"></td>
								</tr>
								<tr>
									<td class="style6" width="100%" align="left" height="19">
										<table width="100%" cellspacing="0" cellpadding="0">

											<tr>
												<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>
												<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
												<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
												<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="order_create_address.php?order_type=2">Request Sign Post Service Call</a></td>
											</tr>
										</table>
									</td>

								</tr>
								<tr>
									<td height="4"><img src="images.pixel_trans.gif" height="4" width="1"></td>
								</tr>
								<tr>
									<td class="style6" width="100%" align="left" height="19">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="30" height="19"><img src="images/pixel_trans.gif" height="19" width="30"></td>

												<td height="20" width="19" align="left" valign="middle"><img src="images/column_arrow.gif" height="9" width="8" /></td>
												<td width="10" height="19"><img src="images/pixel_trans.gif" height="19" width="10"></td>
												<td height="19" class="columnBoxLeftBody" align="left" valign="middle" width="100%" NOWRAP><a class="columnBoxLeftBody" href="order_create_address.php?order_type=3">Request Sign Post Removal</a></td>
											</tr>
										</table>
									</td>
								</tr>
																						<tr>

								<td class="style6" width="100%" align="left" height="19">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td height="19" class="columnBoxLeftBody" align="center" valign="middle" width="100%" NOWRAP>Please login to view your Orders</td>
										</tr>
									</table>
								</td>
							</tr>

													</table>
					  </td>
                    </tr>
                  </table></td>
                </tr>
				<tr>
                  <td height="2" bgcolor="#13688D"><img src="images/pixel_trans.gif" height="2" width="1" /></td>
                </tr>
                <tr>

                  <td height="2"><img src="images/pixel_trans.gif" height="2" width="1" /></td>
                </tr><tr>
                  <td><table width="260" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><img name="body_r1_c9" src="images/my_account_heading_small.jpg" width="260" height="48" border="0" alt="" /></td>
                    </tr>
                    <tr>
                      <td height="109" width="100%" align="center" valign="top" class="columnBox">
					  	<table width="100%" cellspacing="0" cellpadding="0" border="0">

																			<form name="login" action="faq.php?action=login" method="post">
													<tr>
								<td height="2"><img src="images.pixel_trans.gif" height="5" width="1"></td>
							</tr>
													<tr>
								<td class="style6" width="100">Email&nbsp;Address:&nbsp;</td>
								<td><input name="email_address" type="text" size="15"></td>
							</tr>

							<tr>
								<td height="2"><img src="images.pixel_trans.gif" height="2" width="1"></td>
							</tr>
							<tr>
								<td class="style6" width="100">Password:&nbsp;</td>
								<td><input name="password" type="password" size="15"></td>
							</tr>
													<tr>

								<td height="8"><img src="images.pixel_trans.gif" height="8" width="1"></td>
							</tr>
													<tr>
								<td colspan="2" width="100%" height="22" align="center"><input type="image" src="images/buttons/english/button_sign_in.gif" height="22" width="84" alt="Sign In" /></td>
							</tr>
							</form>
														<tr>
								<td height="8"><img src="images.pixel_trans.gif" height="8" width="1"></td>
							</tr>

														<tr>
								<td colspan="2" width="100%" NOWRAP class="columnBoxLeftFooter"><a class="columnBoxLeftFooterRed" href="account_create.php">Sign up here</a> for an Account | <a class="columnBoxLeftFooter" href="forgotten_password.php">Forgotten your Password?</a></td>
							</tr>
												<tr>
							<td height="6"><img src="images/pixel_trans.gif" height="6" width="1" /></td>
						</tr>
						</table>

					  </td>
                    </tr>
                  </table>
		  </td>
                </tr>
				<tr>
                  <td height="2" bgcolor="#13688D"><img src="images/pixel_trans.gif" height="2" width="1" /></td>
                </tr>
                <tr>
                  <td height="2"><img src="images/pixel_trans.gif" height="2" width="1" /></td>

                </tr>
						</table>
					</td>
				</tr>
			  </table></td>
			  <td width="4"><img src="images/pixel_trans.gif" height="1" width="4"></td>
			  <td valign="top"><table width="100%" cellpadding="0" cellspacing="0">
			  	<tr>
					<td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>

				</tr>
				<tr>
				  <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="contentbody">
					<tr>
					  <td height="10" colspan="4"></td>
					</tr>
	
					<tr>
					  <td width="2%"><img src="images/pixel_trans.gif" height="19" width="4" /></td>
					  <td colspan="2" valign="top" align="left"><span class="headerFirstWord">Frequently</span> <span class="headerOtherWords">Asked Questions</span></td>

					  <td width="2%"><img src="images/pixel_trans.gif" height="19" width="4" /></td>
					</tr>
					<tr>
					  <td height="5" colspan="4" bgcolor="#FFFFFF"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
					  <td height="3" colspan="4" bgcolor="#5CA7E1"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
					</tr>
					<tr>

					  <td><img src="images/pixel_trans.gif" height="19" width="1" /></td>
					  <td colspan="2" valign="top"><img src="images/pixel_trans.gif" height="3" width="19" /></td>
					  <td><img src="images/pixel_trans.gif" height="19" width="1" /></td>
					</tr>
					<tr>
					  <td width="4"><img src="image/pixel_trans.gif" height="1" width="4" /></td>
					  <td colspan="2" valign="top" align="left" class="style6">       <table border="0" cellpadding="0" cellspacing="0" width="645">
          <tr>

            <td align="left" valign="top" width="27"><br>
            </td>
            <td align="left" valign="top" width="618">
            <a name="top"></a>
            <p class="purple"><strong>GENERAL SERVICE QUESTIONS</strong></p>
            <li><a href="#1">Q. How fast is your service?</a></li>
            <li><a href="#2">Q. What areas do you cover?</a> </li>

            <li><a href="#3">Q. Which service plan is best for me?</a> </li>
            <li><a href="#4">Q. When can I place my order? </a></li>
            <li><a href="#5">Q. How long will the post remain at the
listing? </a></li>
            <li><a href="#6">Q. Can I get a &#8220;RUSH&#8221; on my SignPost
Installation?</a></li>
            <li><a href="#7">Q. Can I keep the For Sale or Power Punch
riders?</a></li>

            <li><a href="#8">Q. How do you decide where to place my
sign?</a></li>
            <li><a href="#9">Q. I want my post installed in a specific
location, how do I request an exact location?</a></li>
            <li><a href="#10">Q. I don't like where the post is placed,
can I have it moved?</a></li>
            <li><a href="#11">Q. The homeowner has an underground
sprinkler system and/or electric dog fence, what do I do? </a></li>
            <li><a href="#12">Q. Should I call Miss Utility before
placing the signpost installation order? </a><span class="style1">
              <p class="purple">SIGN PANEL QUESTIONS: </p>

              <p> </p>
              </span> </li>
            <li><a href="#13">Q. I am a new agent, can you make sign
panels for me?</a> </li>
            <li><a href="#14">Q. What size sign panels should I order?</a>
            </li>
            <li><a href="#15">Q. How can I get my sign panels to you to
store?</a> </li>

            <li> <a href="#16">Q. How many signpanels should I store
with you?</a></li>
            <li> <a href="#17">Q. Should I keep one or two of my
signpanels?</a><br>
            </li>
            <p><br>
            <span class="style2">PAYMENT QUESTIONS: </span></p>
            <li><a href="#18">Q. How do I pay? </a></li>

            <li> <a href="#19">Q. What additional fees do you charge?</a><span
 class="style1"><br>
              <p class="style2"> WEBSITE QUESTIONS:</p>
              </span></li>
            <li><a href="#20"> Q. My name has disappeared from the web
site drop down list, am I still a customer?</a></li>
            <li> <a href="#21">Q. I can't change the county in your
website drop down list, what do I do? </a></li>

            <li> <a href="#22">Q. I can't place an order via your
website, can I fax or phone in the order? </a></li>
            <li><a href="#23">Q. I can't find ADC map coordinates, and
your website requires them, what do I do?</a></li>
            <li> <a href="#24">Q. Why do you need map coordinates? </a><br>
              <p> <span class="style2">MISCELLANEOUS QUESTIONS:</span>
              <br>

              </p>
            </li>
            <li><a href="#25">Q. My homeowner wants a mailbox
installed, will you provide this service. </a></li>
            <li><a href="#26">Q. What if I don't have a MRIS ID yet?
How do I sign up?</a> </li>
            <li> <a href="#27">Q. What are the legal guidelines for
placing a signpost?</a> </li>
            <li><a href="#28">Q. Do you install directionals? </a><strong><br>

              </strong><font color="#800080"><strong><br>
GENERAL SERVICE QUESTIONS: <a name="generalservicequestions"
 id="generalservicequestions"></a></strong></font></li>
            <p><strong><font color="#800080">Q. How fast is your
service? <a name="1" id="1"></a></font></strong><br>
            <br>
We have a two business day installation window in our CORE VA service
area, with over 90% of jobs being completed in one business day. Please

note that this is a TWO BUSINESS day window, and does not include
weekend days (Saturday or Sunday). In our EXTENDED service area, there
is 3-5 business day installation window . It is three days for all of
Montgomery County , Northern Prince George's County, Southern Frederick
County, and parts of Howard and Anne Arundel counties in MD and all of
Washington DC . It is FIVE business days for Fauquier, Stafford and
Culpepper counties in VA, Northern Frederick and Washington counties in
MD, Franklin and Adams counties in PA and Berkeley and Jefferson
counties in WV. <br>
            <br>
            <a href="#top">^ Back to Top</a> </p>

            <p><strong><font color="#800080">Q. What areas do you
cover? <a name="2" id="2"></a></font></strong><br>
            <br>
Our CORE VA SERVICE AREA includes all of Arlington , Fairfax, Prince
William and Loudon counties.&nbsp; Our CORE MD and DC SERVICE AREA
covers all of Montgomery County, Northern Prince George's County,
Southern Frederick County, and parts of Howard and Anne Arundel
counties in MD and all of Washington DC. Our EXTENDED SERVICE AREA
includes Culpeper, Fauquier, Stafford and Spotsylvania counties in VA,
northern Frederick and Washington counties in MD, Berkeley and
Jefferson counties in WV and Franklin and Adams counties in PA. An
additional installer travel allowance may apply for orders in our
extended service areas. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. Which service plan
is best for me? <a name="3" id="3"></a></font></strong><br>

            <br>
Silver (Signpost Only) &#8211; This service is for the cost conscious agent
and/or the agent who likes to store their own panels Gold (Signpost
plus Panel Storage) &#8211; This service is for the agent who wants the
convenience of having Realty SignPost store and hang their signpanels
when we install the signpost. Platinum (Signpost plus Panel and rider
Storage, For Sale rider, Brochure box and Rider exchange ) &#8211; This
service is our best deal, and is for the agent who wants FULL service
signpost installation. Wants a good deal, and lots of convenience.
Please click here for more information, including prices, for our
service plans. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>

            </a> <strong><font color="#800080">Q. When can I place my
order? <a name="4" id="4"></a></font></strong><br>
            <br>
24 hours a day! However, To get your order scheduled for the next
business day, it must be placed by 5PM. Orders placed after that time
move to the day-after-tomorrow. That means that if an order is placed
at 6PM on Tuesday, the first day that it can normally be installed is
Thursday. NOTE: All Friday and weekend orders are done Monday or
Tuesday.&nbsp; In order for us to provide you our best service, we ask
that you schedule as many jobs as possible on Mondays through
Wednesdays, since Thursday and Fridays are usually our busiest days. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>

            </a> <strong><font color="#800080">Q. How long will the
post remain at the listing? <a name="5" id="5"></a><br>
            <br>
            </font></strong>Current rental period is for four
months.&nbsp; You may extend this time frame past four months by
contacting Realty Sign Post at <a href="mailto:Info@realtysignpost.com">Info@realtysignpost.com
            </a> and telling us how much longer than four months you
will need the post.&nbsp; Realty Sign Post reserves the right to charge
an "Extended Post Rental Fee" for installs left over four months.&nbsp;
Realty Sign Post also reserves the right to remove a post, without
notice, after the four month initial installation period if you have
not contacted us requesting an extension. <br>

            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. Can I get a &#8220;RUSH&#8221;
on my SignPost Installation? <a name="6" id="6"></a></font></strong><br>
            <br>
We do our best to meet your needs, with speedy signpost installations
(90%+ jobs completed the next business day). However, if even faster
service is absolutely necessary, it may be requested via an e-mail to <a
 href="mailto:info@realtysignpost.com">info@realtysignpost.com </a> or
by calling 202 256 0107. If we are able to expedite the installation of
your post, there will be a $35 additional fee. This $35 fee also
applies to emergency Saturday installation orders. <br>

            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a><strong><font color="#800080">Q. Can I keep the For
Sale or Power Punch riders? <a name="7" id="7"></a></font></strong>Just
like our posts, these riders are provided on a rental basis. If you
keep the For Sale or Power Punch riders, you may be charged an
additional fee for the lost/missing riders. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>

            </a> <strong><font color="#800080">SIGNPOST PLACEMENT and
UNDERGROUND UTILITIES: <a name="SIGNPOSTPLACEMENT"
 id="SIGNPOSTPLACEMENT"></a></font></strong><font color="#800080"><strong><br>
            <br>
Q. How do you decide where to place my sign? <a name="8" id="8"></a></strong></font><br>
            <br>
We use several criteria: First, we don't want to damage any underground
utilities, so we avoid any utility location indicators such as gas
meters and transformers. Second, our installers are trained to place
the post where it will get the best visibility with the optimal "line
of sight" exposure. This means that the longer the passing public can
see your sign, the higher the probability they will see it, remember
it, and respond to it. Third, placement of posts can also be influenced
by the desire to avoid trees, and/or not to disturb a yard or
landscaped plants. <br>
            <br>
            <a href="#top">^ Back to Top<br>

            <br>
            </a> <strong><font color="#800080">Q. I want my post
installed in a specific location, how do I request an exact location?<a
 name="9" id="9"></a> </font></strong><br>
            <br>
Please mark the location where you want to post with a flag, stick,
rock or other very obvious object, and please tell us to install the
post by that object in the &#8220;Special Instructions&#8221; section of the order
placement process. We will gladly install the signpost where you or the
homeowner want it to be placed, if you clearly mark the location before
the installer arrives at the address.&nbsp; <br>
            <br>

            <a href="#top">^ Back to Top</a> <strong><font
 color="#800080"><br>
            <br>
Q. I don't like where the post is placed, can I have it moved? <a
 name="10" id="10"></a></font>We will install the signpost where you
want it to be placed, if you clearly mark the location before the
installer arrives at the address.&nbsp;<br>
            <br>
            </strong>If you do not mark a location for the sign post
installation, and ask for the post to be reinstalled at the same
address, Realty Sign Post reserves the right to charge a "Trip Charge"
or an additional "Installation Charge" <br>
            <br>
            <a href="#top">^ Back to Top</a> <strong><font
 color="#800080"><br>

            <br>
Q. The homeowner has an underground sprinkler system and/or electric
dog fence, what do I do? <a name="11" id="11"></a></font></strong>Since
there is no way for our signpost installers to determine where an
underground sprinkler system or electric dog fence is located, the
agent is required to specify an exact spot for the signpost to be
located. Realty Sign Post accepts no responsibility for damage to
underground facilities. Locating and marking such facilities are the
responsibility of the homeowner and the agent. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. Should I call Miss
Utility before placing the signpost installation order? <a name="12"
 id="12"></a></font></strong>Yes, if there are yard lights (gas or
electric) or any visible electrical or telephone boxes (light green in
color) in the yard. This means that there are underground utility lines
at this location and Realty SignPost recommends the realtor or the
homeowner contact Miss Utility, 1-800-552-7001,
www.missutilityofvirginia.com and have the underground utilities
marked, prior to scheduling the signpost installation. By placing this
order (via Internet, phone or fax), you accept full responsibility for
locating and identifying all utility lines, including sprinkler
systems. Neither Realty Sign Post LLC nor its subcontractors accepts
responsibility for damage to unmarked underground facilities. <br>

            <br>
            <a href="#top">^ Back to Top</a> <strong><font
 color="#800080"><br>
            <br>
SIGN PANEL QUESTIONS: <a name="SIGNPANEL" id="SIGNPANEL"></a></font></strong><font
 color="#800080"><strong><br>
            <br>
Q. I am a new agent, can you make sign panels for me? <a name="13"
 id="13"></a></strong></font><br>
            <br>
Sorry, but we don't make signpanels, we quickly and professionally
install signposts. However, there are many local and national companies
that make signpanels. Below is the contact information for four of
these companies. Lowen Sign, <a href="http://www.lowensign.com/">www.lowensign.com
            </a>, 1-800-545-5505 Aztec Marking Co, <a
 href="http://www.aztecsigns.com/">www.aztecsigns.com </a>,
1-800-835-2548, Fax: 1-800-321-7265 Dee Signs, <a
 href="http://www.deesigncompany.com/">www.deesigncompany.com </a>,
1-800-DEE-SIGN Oakley Signs, <a
 href="http://verasend.com/r.html?c=298897&amp;r=298344&amp;t=321353299&amp;l=1&amp;d=85147775&amp;u=http%3a%2f%2fwww%2eoakleysign%2ecom&amp;g=0&amp;f=-1">http://www.oakleysign.com
            </a>, 1-800-373-5330 <br>

            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. What size sign
panels should I order? <a name="14" id="14"></a><br>
            <br>
            </font></strong>The following guidelines are provided so
that you can get the right signs when you order from your sign
provider. Panel size not wider than 30" and 18"-24" deep. Top hole
spacing of 17-19" apart. Inside diameter of top holes about 1/2" and
about 3/8" from top edge of panel. We recommend all holes in the top
and bottom of your signs have grommets (brass liners that make holes
stronger).&nbsp; The grommets nearly double the life of your signs
because they protect the integrity of the sign surface from moisture
penetration and do not tear out easily. Inside diameter of bottom holes
not less than 5/16".&nbsp; Same spacing as your riders. <br>

            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. How can I get my
sign panels to you to store? <a name="15" id="15"></a></font></strong><br>
            <br>
You have several options: 1. You can have them shipped to us at: Realty
Sign Post <br>
C/O Mini U Stor <br>

Attention: RyanW. Myers or H. Douglas Myers <br>
10930 Clara Barton Dr. <br>
Fairfax Station, VA. 22039-1412 Please make sure you have carefully
reviewed and approved the proof, since Realty SignPost assumes no
responsibility for the accuracy of the sign order. 2. We can pick your
panels up from your agency office or a listing. Please e-mail us at
info@ realtysignpost.com to make arrangements for pick up. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. How many signpanels
should I store with you? <a name="16" id="16"></a><br>

            <br>
            </font></strong>We recommend at least two panels per county
that you expect listings in, and a minimum of six signpanels. If you
are a big listing agent, you will obviously need more panels. We have
several warehouses in the Washington DC area, and we place your panels
in the appropriate warehouse in order to provide you fast service. We
can not guarantee a signpanel will be installed at a property on the
same day that the panel is removed from a different property. This is
due to the fact that Realty SignPost has multiple installers working
multiple installation areas, and the same installer may not complete
both the install and removal. Please allow at least two business days
to complete a removal and two business days to complete the install. <br>
            <br>
            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. Should I keep one or
two of my signpanels? <a name="17" id="17"></a></font></strong><br>
            <br>

Yes, we recommend agents keep a few of their signpanels for the unique
situations that occur from time to time. For example, you may get a
condo listing, or we may be able to complete a RUSH signpost
installation, but not be able to get the signpanel installed in a RUSH
manner. <br>
            <br>
            <a href="#top">^ Back to Top </a></p>
            <p><a href="#top"><br>
            </a> <strong><font color="#800080">PAYMENT QUESTIONS: <a
 name="paymentquestion" id="paymentquestion"></a></font></strong><font
 color="#800080"><strong><br>
            <br>
Q. How do I pay? <a name="18" id="18"></a></strong></font><br>

            <br>
For new customers, we require payment via credit card. Please click
here to download our Credit Card Authorization form. Please Print it
out, complete the form and fax it to 703 995 4567. You only need to
complete this form once. FYI, we are working on an online credit card
payment system. For certain agencies, we bill on a monthly basis.
Please check with your broker to see if your agency has a monthly
billing agreement with Realty SignPost. <br>
            <br>
            <a href="#top">^ Back to Top</a> </p>
            <p> <strong><font color="#800080">Q. What additional fees
do you charge? <a name="19" id="19"></a><br>
            <br>
            </font></strong>We strive to provide fast, dependable and
professional service. In order to continue to provide this level of
quality service, and to encourage agents to provide the information we
need to deliver quality service, we charge the following fees in the
following situations. Summary List of Additional Charges: Lost/Damaged
Post: $60 Declined Credit Card Fee $25 Lost/Damaged metal anchor: $20
Trip Charge: $20 Missing Incorrect Map Coordinates: $15 Incorrect
Address Information: $10 Fax/Voice Mail Order Processing: $10 Missing
Riders at Removal: $10 per rider Missing Hooks/Clips at Removal $10
Missing Brochure Box at Removal $10 Installer Travel Allowance Varies
by Location Late Payment Fee $10 <br>

            <br>
            <a href="#top">^ Back to Top</a> <br>
            <br>
            <strong><font color="#800080">WEBSITE QUESTIONS: </font></strong><font
 color="#800080"><strong><br>
            <br>
Q. My name has disappeared from the web site drop down list, am I still
a customer? <a name="20" id="20"></a></strong></font><br>
            <br>

Yes, your still a valued customer. Your name and information is stored
in what are called &#8216;cookies' on your specific computer. If your
information disappears, it is due to one of three reasons. 1. The
&#8216;cookies' on your computer have been cleared. 2. You are at a new
computer that you haven't placed an order yet. 3. Your computer is
configured to not allow the website to store &#8216;cookies'. For the first
two reasons, simply enter your information, and it will be stored for
the next time you place an order. For the third reason, talk to your IT
person and have them configure your computer to allow cookies from
Realtysignpost.com <br>
            <br>
            <a href="#top">^ Back to Top </a> <br>
            <br>
            <strong><font color="#800080"><br>
Q. I can't change the county in your website drop down list, what do I
do? <a name="21" id="21"></a><br>

            <br>
            </font></strong>Our website works best with the latest
versions of Microsoft Internet Explorer, V6.0 as of Nov 05 and Mozilla
Firefox, V1.0.7. If you don't have the latest versions, the Java
enabled dropdown lists may not work correctly. We recommend you
download and install the free upgrade for your web browsers. Go to <a
 href="http://www.microsoft.com/">www.microsoft.com </a> to upgrade
internet explorer or go to <a href="http://www.mozilla.org/">www.mozilla.org
            </a> to upgrade Netscape's Mozilla Firefox. Please consult
with your IT person if you are uncomfortable upgrading software on your
own. Upgrading your browser provides many other benefits, including
updated features and the latest security against hackers and viruses. <br>
            <br>
            <a href="#top">^ Back to Top</a> <br>

            <br>
            <strong><font color="#800080">Q. I can't place an order via
your website, can I fax or phone in the order? <a name="22" id="22"></a></font></strong><br>
            <br>
Yes, orders can be faxed to 703 995 4567 or 202 478 2131. Voicemail
orders may also be left at the numbers by calling those numbers and
pressing &#8216;1' to leave a recording. We prefer and encourage website
orders, since it offers many advantages over fax and phone orders.
These include an e-mail verifying placement of your order and easier
removal of the post (if cookies are enabled on your computer). <br>
            <br>
            <a href="#top">^ Back to Top</a><br>
            <br>

            <font color="#800080"><strong>Q. I can't find ADC map
coordinates, and your website requires them, what do I do? <a name="23"
 id="23"></a><br>
            </strong></font></p>
            <p>ADC mapbooks are one of the standard map books for the
Washington DC area, and are usually included in MRIS listings. These
books are available at any 7-11 store , most convenience stores,
Realtor stores, and many other stores. The books can also be ordered
via <a href="http://www.adcmap.com/">www.adcmap.com </a>. If you
don't have quick access to an ADC map book, you can look at old MRIS
listings for this property, or other properties on the same street or
in the same development to see if the listing realtor included ADC map
coordinates. Even if the property is new construction, it will still
have ADC map grid coordinates. Simply look up the coordinates of the
closest major road/intersection. For new construction, accurate
directions and map coordinates are even more important to enable to
locate the property in a timely manner. ADC or Thomas Brother map
coordinates are required. If you just have Thomas Brother map
coordinates, you may enter 1-A-1 for the ADC map coordinates, and the
order will go through. Entering incorrect map coordinates or not
providing map coordinates invalidates our &#8220;Two Business Day&#8221;
installation policy and may result in a $15 charge to look up map
coordinates. <br>
            <br>
            <a href="#top">^ Back to Top<br>

            <br>
            </a><a href="#top"> <br>
            </a> <strong><font color="#800080">Q. Why do you need map
coordinates? <a name="24" id="24"></a></font></strong><br>
            <br>
In order to continue provide FAST and DEPENDABLE signpost installation
service, we need to be able to find each and every property quickly.
Our experience is that if we have an accurate house number and street,
good directions and map coordinates allows your job to be assigned to
the correct installer and allows the installer to be able to use
mapping software, the Internet and maps to locate the property in a
timely fashion. If any of that information is missing, it often takes
disproportionately longer to find the correct house. So far, our worst
example is an agent who ordered an installation for a house in Mclean,
but the house was actually in Manassas . Obviously, this created a
great delay for all the other jobs that installer had to complete that
day. <br>
            <br>
            <a href="#top">^ Back to Top<br>

            <br>
            </a><a href="#top"> <br>
            </a><font color="#800080"><strong>MISCELLANEOUS QUESTIONS: <a
 name="MISCELLANEOUSQUESTIONS" id="MISCELLANEOUSQUESTIONS"></a></strong><strong><br>
            <br>
Q. My homeowner wants a mailbox installed, will you provide this
service. <a name="25" id="25"></a></strong></font><br>
            <br>
Yes and No! J Most of my installers are happy to install a mailbox, but
they are responsible to working out the details with the homeowner. It
will not be a business transaction associated with Realty SignPost.
Just e-mail us the address and phone number of the person to contact
and an installer will contact the person, and work out a price. <br>
            <br>

            <a href="#top">^ Back to Top<br>
            <br>
            </a> <strong><font color="#800080">Q. What if I don't have
a MRIS ID yet? How do I sign up? <a name="26" id="26"></a></font></strong><br>
            <br>
Simply e-mail us at <a href="mailto:info@realtysignpost.com">info@realtysignpost.com
            </a> to get a temporary MRIS ID for our system. Once you
have your actual MRIS ID, e-mail it to us, and we will update your file
in our database. <br>
            <br>

            <a href="#top">^ Back to Top<br>
            <br>
            </a> <font color="#800080"><strong>Q. What are the legal
guidelines for placing a signpost? <a name="27" id="27"></a></strong></font><br>
            <br>
Click here for the latest information we have on legal guidelines.<br>
            <br>
            <a href="#top">^ Back to Top</a></p>

            <p><a href="#top"><br>
            </a><font color="#800080"><strong>Q. Do you install
directionals? <a name="28" id="28"></a></strong></font></p>
            <p>No. We have provided this service in the past, and found
that it is best for the agent to install directionals. This is due to
the fact that directionals often quickly disappear, and we don't always
go to a property via the same streets that the agent would like the
directionals posted. <br>
            <br>
            <a href="#top">^ Back to Top</a> </p>
            <b><font color="#800080" face="Arial" size="3"><br>
            <br>

            </font></b> </td>
          </tr>
          <tr>
            <td style="vertical-align: top;"><br>
            </td>
            <td style="vertical-align: top;"><br>
            </td>
          </tr>

      </table></td>
					  <td width="4"><img src="image/pixel_trans.gif" height="1" width="4" /></td>
					</tr>
					<tr>
					  <td height="53" colspan="4"><img src="image/pixel_trans.gif" height="53" width="1" /></td>
					</tr>
				  </table></td>
				  </tr>
				 </table></td>

            </tr>
        </table></td>
		<td width="1"><img src="images/pixel_trans.gif" height="1" width="1" /></td>
        <td width="11" background="images/body_r1_c13.jpg"><img src="images/pixel_trans.gif" height="1" width="11" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

      <tr>
        <td width="12"><img src="images/bot0.jpg" width="12" height="73" /></td>
        <td width="100%" align="center" valign="middle" background="images/bot1.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
    <td width="4"><img src="images/pixel_trans.gif" height="1" width="4" /></td>
     <td class="style6" align="left" NOWRAP valign="top"><a class="footerMenu" href="index.php">Home</a> | <a class="footerMenu" href="about_us.php">About Us</a> | <a class="footerMenu" href="faq.php">FAQ</a> | <a class="footerMenu" href="terms_of_service.php">Terms of Service</a> | <a class="footerMenu" href="account.php">My Account</a> | <a class="footerMenu" href="service_plans.php">Service Plans</a> | <a class="footerMenu" href="contact_us.php">Contact Us</a> | <a class="footerMenu" href="index.php?action=logout">Logoff</a>&nbsp;&nbsp;</td>

     <td class="style6" align="right">P.O. Box 641, McLean, VA 22101-0641 | Email: info@realtysignpost.com | Fax to: 703-995-4567 or 202-478-2131 | Emergency Issue Resolution: 202 256 0107</td>
	 <td width="4"><img src="images/pixel_trans.gif" height="1" width="4" /></td>
</tr>
<tr>
      <td width="4"><img src="images/pixel_trans.gif" height="1" width="4" /></td>
      <td></td>
	  <td align="right" class="style6">&copy; Copyright 2007 Realty Sign Post</td>
      <td width="4"><img src="images/pixel_trans.gif" height="1" width="4" /></td>

</tr>
        </table></td>
        <td width="12"><img src="images/bot2.jpg" width="12" height="73" /></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
