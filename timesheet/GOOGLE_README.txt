TimeSheet Google Calendar Extension

About:

TimeSheet Google Calendar Extension is a Java Web Based application that provides asynchronous  integration between DotProject's Timesheet Module and Google Calendar. TimeSheet Google Calendar Extension takes use of JBoss Seam Integration Framework, Hibernate, Google Data API and Quartz API to deliver and easy to use way of registering Tasks Logs. Take advantage of Google's great User Interface, security and reliability.

Installation:

JBoss Application users simply drop the binary .war into JBoss deployment folder and provide the Database Datasource with "DotCalendarSeamDatasource" as it's JNDI name , that's it!  It uses JBoss Seam for now*,  so for more information on deployment on other Servlet Containers and Application Servlets, please refer to http://docs.jboss.com/seam/2.0.1.GA/reference/en/html/configuration.html#config.install.embedded
  
   <local-tx-datasource>
    ...
      <jndi-name>DotCalendarSeamDatasource</jndi-name>
      <connection-url>jdbc:mysql://192.168.1.1:3306/dotproject</connection-url>  //change this line to your url!
      <driver-class>com.mysql.jdbc.Driver</driver-class>
      <user-name>dotproject_username</user-name>
      <password>dotproject_password</password>
    ...
   </local-tx-datasource>
   



Usage:

After installing DotProject's Timesheet Module, login as administrator and enable Timesheet Module. Setup the primary and secondary security keys - which can be any 16-bit-alphanumeric keys - it's used to encrypt and decrypt all User's Google's passwords from and to the database. Setup the synchronization interval for created Tasks and TaskLogs. TimeSheet Google Calendar Extension will update and create new Calendars on the user's configured Google Calendar account for each corresponding Task existing on the DotProject Database. Also, every Entry registered under a Task Calendar automatically created on Google Calendar will be inserted into the DotProject's Database with it's amount of hours dynamically calculated. Deploy the application - just put the war file in JBoss' deploy folder - and check you Google Calendar! All your Tasks will be registered there.
As DotProject user, go to the TimeSheet Module and setup you Google Account Information, email address and password. That's pretty much it!

Known Bugs/Issues:

TimeSheet Google Calendar Extension still have problems to handle special characters in Google Calendar. It's a pretty simple problem, but I just couldn't get time to fix it. So, be my guest :)
* Seam Dependency. Unfortunately I wasn't able to use Seam Dependency Injection the correct way. The whole application is started thought a Servlet, so it stays outside Seam context, it's handled manually. It was great for kickstarting the project as fast as possible, but it will soon be migrated to pure/simple Hibernate or JPA.

WishList:

* Log the Tasks with it's start and end date/time;
* Mail reports;
* Calendar Sharing support;
* Parametrized Google Calendar Query interval. For now it only retrieves the user's past 30 days Calendar entries. Hardcoded! OUCH!

